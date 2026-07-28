<?php

namespace App\Http\Controllers;

use App\Enums\ScreeningStatus;
use App\Models\Screening;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ScreeningHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $screenings = Screening::query()
            ->whereBelongsTo($request->user())
            ->completed()
            ->latest('completed_at')
            ->paginate(10);

        return view('history.index', [
            'screenings' => $screenings,
        ]);
    }

    public function show(Request $request, Screening $screening): View
    {
        $this->authorize('view', $screening);
        abort_if($screening->status !== ScreeningStatus::Completed, 404);

        return view('history.show', [
            'screening' => $screening->load([
                'answers',
            ]),
        ]);
    }
}
