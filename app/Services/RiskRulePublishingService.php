<?php

namespace App\Services;

use App\Enums\VersionStatus;
use App\Models\RiskRuleVersion;
use Illuminate\Support\Facades\DB;
use LogicException;

class RiskRulePublishingService
{
    public function publish(RiskRuleVersion $version, int $publisherId): RiskRuleVersion
    {
        if ($version->status !== VersionStatus::Draft) {
            throw new LogicException('Only draft risk rules can be published.');
        }

        return DB::transaction(function () use ($version, $publisherId): RiskRuleVersion {
            RiskRuleVersion::query()
                ->where('status', VersionStatus::Published)
                ->update(['status' => VersionStatus::Archived]);

            $version->forceFill([
                'status' => VersionStatus::Published,
                'published_at' => now(),
                'published_by' => $publisherId,
            ])->save();

            return $version->fresh();
        });
    }
}
