<?php
namespace App\Helpers;

use Illuminate\Support\Collection;
use App\Helpers\SystemHelper;

class StoryBookHelper
{
    public const STATUS_DRAFT    = 'Draft';
    public const STATUS_ONGOING    = 'Ongoing';
    public const STATUS_PENDING    = 'Pending';
    public const STATUS_COMPLETE    = 'Complete';

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

    public static function continuities(): Collection
    {
        return SystemHelper::toOptions([
            self::CONTINUITY_STANDALONE,
        ]);
    }
}
