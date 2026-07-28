<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationPost;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\RiskRuleVersion;
use App\Models\Screening;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $trendDates = collect(range(13, 0))
            ->map(fn (int $daysAgo): CarbonImmutable => CarbonImmutable::today()->subDays($daysAgo));

        $stats = Cache::remember('admin.dashboard.stats', now()->addMinutes(5), fn (): array => [
            'users' => User::query()->count(),
            'completed_screenings' => Screening::query()->completed()->count(),
            'questions' => Question::query()->count(),
            'active_questions' => Question::query()->where('is_active', true)->count(),
            'risk_rule_versions' => RiskRuleVersion::query()->count(),
            'education_posts' => EducationPost::query()->count(),
            'published_questionnaires' => QuestionnaireVersion::query()->published()->count(),
        ]);

        $riskDistributionChart = Cache::remember('admin.dashboard.risk-distribution', now()->addMinutes(5), function (): array {
            $riskDistribution = Screening::query()
                ->completed()
                ->selectRaw('risk_label_snapshot, COUNT(*) as total')
                ->groupBy('risk_label_snapshot')
                ->pluck('total', 'risk_label_snapshot');

            return [
                'labels' => $riskDistribution->keys()->filter()->values()->all(),
                'values' => $riskDistribution->values()->all(),
            ];
        });

        $trendChart = Cache::remember('admin.dashboard.trend', now()->addMinutes(5), function () use ($trendDates): array {
            $trendData = Screening::query()
                ->completed()
                ->selectRaw('DATE(completed_at) as completed_date, COUNT(*) as total')
                ->whereDate('completed_at', '>=', CarbonImmutable::today()->subDays(13)->toDateString())
                ->groupBy('completed_date')
                ->pluck('total', 'completed_date');

            return [
                'labels' => $trendDates->map(fn (CarbonImmutable $date): string => $date->translatedFormat('d M'))->all(),
                'values' => $trendDates->map(fn (CarbonImmutable $date): int => (int) ($trendData[$date->toDateString()] ?? 0))->all(),
            ];
        });

        return view('admin.dashboard', [
            'stats' => $stats,
            'riskDistributionChart' => $riskDistributionChart,
            'trendChart' => $trendChart,
        ]);
    }
}
