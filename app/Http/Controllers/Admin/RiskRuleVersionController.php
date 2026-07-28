<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VersionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertRiskRuleVersionRequest;
use App\Models\QuestionnaireVersion;
use App\Models\RiskRuleVersion;
use App\Services\AuditLogService;
use App\Services\RiskRulePublishingService;
use App\Services\RiskRuleValidationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LogicException;

class RiskRuleVersionController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->string('status'));

        $versions = RiskRuleVersion::query()
            ->withCount(['riskLevels'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.risk-rules.index', [
            'versions' => $versions,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.risk-rules.form', [
            'version' => new RiskRuleVersion([
                'is_demo_data' => true,
                'medical_approval_required' => true,
            ]),
            'riskLevels' => [
                [
                    'name' => '',
                    'slug' => '',
                    'min_score' => 0,
                    'max_score' => 0,
                    'semantic_color' => 'success',
                    'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                    'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                    'is_active' => true,
                ],
            ],
            'formAction' => route('admin.risk-rules.store'),
            'formMethod' => 'POST',
            'pageTitle' => 'Buat Draft Aturan Risiko',
        ]);
    }

    public function store(
        UpsertRiskRuleVersionRequest $request,
        RiskRuleValidationService $riskRuleValidationService,
        AuditLogService $auditLogService,
    ): RedirectResponse {
        $version = DB::transaction(function () use ($request, $riskRuleValidationService): RiskRuleVersion {
            $version = RiskRuleVersion::query()->create([
                'version_number' => ((int) RiskRuleVersion::query()->max('version_number')) + 1,
                'title' => $request->validated('title'),
                'status' => VersionStatus::Draft,
                'max_score_covered' => 0,
                'is_demo_data' => (bool) $request->boolean('is_demo_data', true),
                'medical_approval_required' => (bool) $request->boolean('medical_approval_required', true),
            ]);

            $this->syncRiskLevels($version, $request->validated('risk_levels'));
            $maxScoreCovered = (int) $version->riskLevels()->max('max_score');
            $version->forceFill(['max_score_covered' => $maxScoreCovered])->save();
            $riskRuleValidationService->assertCoverage($version, $maxScoreCovered);

            return $version->fresh();
        });

        $auditLogService->record(
            $request->user(),
            'admin.risk-rule.created',
            $version,
            ['title' => $version->title, 'status' => $version->status->value],
            $request->ip(),
        );

        return redirect()
            ->route('admin.risk-rules.show', $version)
            ->with('status', 'Draft aturan risiko berhasil dibuat.');
    }

    public function show(RiskRuleVersion $riskRule): View
    {
        $publishedQuestionnaire = QuestionnaireVersion::query()
            ->published()
            ->latest('published_at')
            ->first();

        return view('admin.risk-rules.show', [
            'version' => $riskRule->load(['riskLevels', 'publisher']),
            'currentQuestionnaireMaxScore' => $publishedQuestionnaire?->max_score_snapshot,
        ]);
    }

    public function edit(RiskRuleVersion $riskRule): View
    {
        abort_unless($riskRule->status === VersionStatus::Draft, 404);

        return view('admin.risk-rules.form', [
            'version' => $riskRule->load('riskLevels'),
            'riskLevels' => $riskRule->riskLevels->map(fn ($level) => [
                'name' => $level->name,
                'slug' => $level->slug,
                'min_score' => $level->min_score,
                'max_score' => $level->max_score,
                'semantic_color' => $level->semantic_color,
                'description' => $level->description,
                'recommendation' => $level->recommendation,
                'is_active' => $level->is_active,
            ])->all(),
            'formAction' => route('admin.risk-rules.update', $riskRule),
            'formMethod' => 'PUT',
            'pageTitle' => 'Edit Draft Aturan Risiko',
        ]);
    }

    public function update(
        UpsertRiskRuleVersionRequest $request,
        RiskRuleVersion $riskRule,
        RiskRuleValidationService $riskRuleValidationService,
        AuditLogService $auditLogService,
    ): RedirectResponse {
        abort_unless($riskRule->status === VersionStatus::Draft, 404);

        DB::transaction(function () use ($request, $riskRule, $riskRuleValidationService): void {
            $riskRule->forceFill([
                'title' => $request->validated('title'),
                'is_demo_data' => (bool) $request->boolean('is_demo_data', true),
                'medical_approval_required' => (bool) $request->boolean('medical_approval_required', true),
            ])->save();

            $this->syncRiskLevels($riskRule, $request->validated('risk_levels'));
            $maxScoreCovered = (int) $riskRule->riskLevels()->max('max_score');
            $riskRule->forceFill(['max_score_covered' => $maxScoreCovered])->save();
            $riskRuleValidationService->assertCoverage($riskRule, $maxScoreCovered);
        });

        $auditLogService->record(
            $request->user(),
            'admin.risk-rule.updated',
            $riskRule,
            ['title' => $riskRule->title],
            $request->ip(),
        );

        return redirect()
            ->route('admin.risk-rules.show', $riskRule)
            ->with('status', 'Draft aturan risiko berhasil diperbarui.');
    }

    public function publish(
        Request $request,
        RiskRuleVersion $riskRule,
        RiskRulePublishingService $publishingService,
        RiskRuleValidationService $riskRuleValidationService,
        AuditLogService $auditLogService,
    ): RedirectResponse {
        abort_unless($riskRule->status === VersionStatus::Draft, 404);

        $publishedQuestionnaire = QuestionnaireVersion::query()
            ->published()
            ->latest('published_at')
            ->first();

        if (! $publishedQuestionnaire) {
            return back()->withErrors([
                'publish' => 'Versi kuesioner terpublikasi belum tersedia untuk validasi aturan risiko.',
            ]);
        }

        try {
            $riskRuleValidationService->assertCoverage($riskRule, (int) $publishedQuestionnaire->max_score_snapshot);
            $publishedVersion = $publishingService->publish($riskRule, $request->user()->id);
        } catch (LogicException $exception) {
            return back()->withErrors([
                'publish' => $exception->getMessage(),
            ]);
        }

        $auditLogService->record(
            $request->user(),
            'admin.risk-rule.published',
            $publishedVersion,
            ['title' => $publishedVersion->title],
            $request->ip(),
        );

        return redirect()
            ->route('admin.risk-rules.show', $publishedVersion)
            ->with('status', 'Versi aturan risiko berhasil dipublikasikan.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $riskLevels
     */
    protected function syncRiskLevels(RiskRuleVersion $version, array $riskLevels): void
    {
        $version->riskLevels()->delete();

        foreach (array_values($riskLevels) as $index => $riskLevel) {
            $version->riskLevels()->create([
                'name' => $riskLevel['name'],
                'slug' => $riskLevel['slug'],
                'min_score' => (int) $riskLevel['min_score'],
                'max_score' => (int) $riskLevel['max_score'],
                'semantic_color' => $riskLevel['semantic_color'],
                'description' => $riskLevel['description'],
                'recommendation' => $riskLevel['recommendation'],
                'display_priority' => $index + 1,
                'is_active' => (bool) ($riskLevel['is_active'] ?? false),
            ]);
        }
    }
}
