<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $logs = AuditLog::query()
            ->with('actor')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('action', 'like', "%{$search}%");
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'search' => $search,
        ]);
    }
}
