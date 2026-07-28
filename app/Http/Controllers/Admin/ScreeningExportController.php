<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScreeningExportController extends Controller
{
    public function __invoke(
        ScreeningController $screeningController,
        AuditLogService $auditLogService,
    ): StreamedResponse {
        $query = $screeningController->filteredQuery(request())
            ->with('user')
            ->latest('completed_at');

        $auditLogService->record(
            request()->user(),
            'admin.screenings.exported',
            null,
            request()->only(['risk_label', 'user_email', 'date_from', 'date_to']),
            request()->ip(),
        );

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Tanggal Selesai',
                'Nama Pengguna',
                'Email',
                'Usia Kehamilan',
                'Skor',
                'Kategori Risiko',
                'Versi Kuesioner',
                'Versi Aturan Risiko',
            ]);

            $query->chunk(200, function ($screenings) use ($handle): void {
                foreach ($screenings as $screening) {
                    fputcsv($handle, [
                        optional($screening->completed_at)->format('Y-m-d H:i:s'),
                        $screening->user?->name,
                        $screening->user?->email,
                        trim("{$screening->gestational_age_weeks_snapshot} minggu {$screening->gestational_age_days_snapshot} hari"),
                        "{$screening->total_score}/{$screening->max_score}",
                        $screening->risk_label_snapshot,
                        $screening->questionnaire_version_name_snapshot,
                        $screening->risk_rule_version_name_snapshot,
                    ]);
                }
            });

            fclose($handle);
        }, 'screenings-export-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
