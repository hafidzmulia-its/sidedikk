<?php

namespace App\Services;

use App\Enums\VersionStatus;
use App\Models\QuestionnaireVersion;
use Illuminate\Support\Facades\DB;
use LogicException;

class QuestionnairePublishingService
{
    public function publish(QuestionnaireVersion $version, int $publisherId): QuestionnaireVersion
    {
        if ($version->status !== VersionStatus::Draft) {
            throw new LogicException('Only draft questionnaires can be published.');
        }

        return DB::transaction(function () use ($version, $publisherId): QuestionnaireVersion {
            QuestionnaireVersion::query()
                ->where('status', VersionStatus::Published)
                ->update(['status' => VersionStatus::Archived]);

            $version->forceFill([
                'status' => VersionStatus::Published,
                'published_at' => now(),
                'published_by' => $publisherId,
                'max_score_snapshot' => (int) $version->questions()->where('is_active', true)->sum('score_yes'),
            ])->save();

            return $version->fresh();
        });
    }
}
