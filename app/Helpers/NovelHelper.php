<?php
namespace App\Helpers;

use Illuminate\Support\Collection;
use App\Helpers\SystemHelper;

class StoryHelper
{
    public const STATUS_DRAFT    = 'Draft';
    public const STATUS_ONGOING    = 'Ongoing';
    public const STATUS_PENDING    = 'Pending';
    public const STATUS_COMPLETE    = 'Complete';

    public const STEP_STATUS_DRAFT    = 'Draft';
    public const STEP_STATUS_ONGOING    = 'Ongoing';
    public const STEP_STATUS_PENDING    = 'Pending';
    public const STEP_STATUS_COMPLETE    = 'Complete';
    public const STEP_STATUS_FAILED    = 'Failed';
    public const STEP_STATUS_CANCELLED    = 'Cancelled';

    public const CONTINUITY_STANDALONE    = 'Standalone';

    public static function statuses(): Collection
    {
        return SystemHelper::toOptions([
            self::STATUS_DRAFT,
            self::STATUS_ONGOING,
            self::STATUS_PENDING,
            self::STATUS_COMPLETE,
        ]);
    }

    public static function stepStatuses(): Collection
    {
        return SystemHelper::toOptions([
            self::STEP_STATUS_DRAFT,
            self::STEP_STATUS_ONGOING,
            self::STEP_STATUS_PENDING,
            self::STEP_STATUS_FAILED,
            self::STEP_STATUS_CANCELLED,
            self::STEP_STATUS_COMPLETE,
        ]);
    }

    public static function continuities(): Collection
    {
        return SystemHelper::toOptions([
            self::CONTINUITY_STANDALONE,
        ]);
    }
}
