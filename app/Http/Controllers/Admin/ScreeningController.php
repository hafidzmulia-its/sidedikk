<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Screening;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    public function index(Request $request): View
    {
        $screenings = $this->filteredQuery($request)
            ->with(['user'])
            ->latest('completed_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.screenings.index', [
            'screenings' => $screenings,
            'filters' => $this->filters($request),
        ]);
    }

    public function show(Screening $screening): View
    {
        abort_unless($screening->status->value === 'completed', 404);

        return view('admin.screenings.show', [
            'screening' => $screening->load(['user', 'answers', 'questionnaireVersion', 'riskRuleVersion']),
        ]);
    }

    public function filteredQuery(Request $request): Builder
    {
        $filters = $this->filters($request);

        return Screening::query()
            ->completed()
            ->when($filters['risk_label'] !== '', fn ($query) => $query->where('risk_label_snapshot', $filters['risk_label']))
            ->when($filters['user_email'] !== '', function ($query) use ($filters): void {
                $query->whereHas('user', fn ($userQuery) => $userQuery->where('email', 'like', '%'.$filters['user_email'].'%'));
            })
            ->when($filters['date_from'] !== '', fn ($query) => $query->whereDate('completed_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($query) => $query->whereDate('completed_at', '<=', $filters['date_to']));
    }

    /**
     * @return array{risk_label:string,user_email:string,date_from:string,date_to:string}
     */
    protected function filters(Request $request): array
    {
        return [
            'risk_label' => trim((string) $request->string('risk_label')),
            'user_email' => trim((string) $request->string('user_email')),
            'date_from' => trim((string) $request->string('date_from')),
            'date_to' => trim((string) $request->string('date_to')),
        ];
    }
}
