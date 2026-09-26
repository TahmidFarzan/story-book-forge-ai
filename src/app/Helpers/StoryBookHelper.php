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

    public const TEXT_GENERATION_STEP_COUNT = 15;

    public const ILLUSTRATION_GENERATION_STEP = 16;

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
