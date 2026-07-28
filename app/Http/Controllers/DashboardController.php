<?php

namespace App\Http\Controllers;

use App\Models\EducationPost;
use App\Services\PregnancyAgeService;
use App\Services\ScreeningService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly PregnancyAgeService $pregnancyAgeService,
        private readonly ScreeningService $screeningService,
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $userScreenings = $user->screenings();

        $activeScreening = (clone $userScreenings)
            ->inProgress()
            ->latest('started_at')
            ->first();

        return view('dashboard', [
            'user' => $user,
            'pregnancyAge' => $this->pregnancyAgeService->calculateFromHpht($user?->hpht_date?->toDateString()),
            'activeScreening' => $activeScreening,
            'activeScreeningNextStep' => $activeScreening
                ? $this->screeningService->nextStepFor($activeScreening)
                : null,
            'latestScreening' => (clone $userScreenings)
                ->where('status', 'completed')
                ->latest('completed_at')
                ->first(),
            'educationPosts' => EducationPost::query()
                ->published()
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
