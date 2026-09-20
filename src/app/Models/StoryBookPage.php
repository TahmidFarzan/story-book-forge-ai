<?php

namespace App\Models;


use App\Helpers\MediaHelper;
use App\Observers\StoryBookPageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Table('story_book_pages')]
#[Fillable([
    'story_book_id',
    'no',
    'narration',
    'illustration_prompt',
    'illustration_type_prompt_instruction',
    'created_by_id',
])]
#[ObservedBy([StoryBookPageObserver::class])]

class StoryBookPage extends Model   implements HasMedia
{
    use HasFactory, HasSlug, LogsActivity, InteractsWithMedia;

    protected $appends = ['media_collection_name'];

    protected function casts(): array
    {
        return [
            'no' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'story_book_id',
                'no',
                'narration',
                'illustration_prompt',
                'illustration_type_prompt_instruction',
            ])
            ->useLogName('StoryBookPage')
            ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
            ->logOnlyDirty()
            ->logExcept([
                'id',
                'created_by_id',
                'created_at',
            ])
            ->dontLogEmptyChanges();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->saveSlugsTo('slug')
            ->generateSlugsFrom(function ($model) {
                $mainSlug = Str::random(5);
                $currentdatetime = now()->format('Ymdhis');
                return "{$model->no}-{$currentdatetime}-{$mainSlug}";
            })
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(255)
            ->usingSuffixGenerator(fn() => Str::lower(Str::random(5)));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->media_collection_name);
    }

    public function registerMediaConversions($spatieMedia = null): void
    {
        $this->addMediaConversion(MediaHelper::DEFAULT_CONVERSION)
            ->format(MediaHelper::DEFAULT_CONVERSION_FORMAT)
            ->quality(100)
            ->performOnCollections($this->media_collection_name)
            ->queued();
    }

    public function getMediaCollectionNameAttribute(): string
    {
        return "Story Book Page";
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function latestActivityLog(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')->latestOfMany();
    }

    public function storyBook(): BelongsTo
    {
        return $this->belongsTo(StoryBook::class);
    }

    public function illustrationImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', $this->media_collection_name)
            ->whereJsonContains('custom_properties->role', MediaHelper::ROLE_STORY_BOOK_PAGE_ILLUSTRATION);
    }
}
