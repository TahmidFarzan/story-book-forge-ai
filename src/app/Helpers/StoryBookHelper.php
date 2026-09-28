<?php
namespace App\Helpers;

use Illuminate\Support\Collection;
use App\Helpers\SystemHelper;

class StoryBookHelper
{
    public const STATUS_DRAFT                    = 'Draft';
    public const STATUS_PROCESSING_TEXT          = 'Processing Text';
    public const STATUS_STOP_TEXT                = 'Stop Text';
    public const STATUS_COMPLETE_TEXT            = 'Complete Text';
    public const STATUS_PROCESSING_ILLUSTRATION  = 'Processing Illustration';
    public const STATUS_STOP_ILLUSTRATION        = 'Stop Illustration';
    public const STATUS_COMPLETE                 = 'Complete';

    public const FIRST_GENERATION_STAGE = 1;

    public const SECOND_GENERATION_STAGE = 2;

    public const FINAL_GENERATION_STAGE = 3;

    public const TEXT_GENERATION_STAGE_COUNT = 2;

    public const TOTAL_GENERATION_STAGE_COUNT = 3;

    public static function statuses(): Collection
    {
        return SystemHelper::toOptions([
            self::STATUS_DRAFT,
            self::STATUS_PROCESSING_TEXT,
            self::STATUS_STOP_TEXT,
            self::STATUS_COMPLETE_TEXT,
            self::STATUS_PROCESSING_ILLUSTRATION,
            self::STATUS_STOP_ILLUSTRATION,
            self::STATUS_COMPLETE,
        ]);
    }

    public static function isLocked(?string $status): bool
    {
        return $status === self::STATUS_COMPLETE;
    }
}
