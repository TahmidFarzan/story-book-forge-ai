<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use App\Helpers\StoryBookHelper;
use App\Observers\StoryBookObserver;
use App\Policies\StoryBookPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


#[Table('story_books')]
#[Fillable([
    'title',
    'sub_title',
    'datetime',
    'slug',
    'status',
    'audience_id',
    'story_book_type_id',
    'language_id',
    'illustration_type_id',
    'ai_brain_text_id',
    'ai_brain_illustration_id',
    'additional_information',
    'ai_prompt',
    'foundation',
    'characters',
    'world_bible',
    'locations',
    'factions',
    'creatures',
    'systems',
    'timeline',
    'story_structure',
    'twists_and_foreshadowing',
    'scene_plans',
    'dialogue_plans',
    'page_plan',
    'current_step',
    'completed_steps_count',
    'current_illustration_page',
    'completed_illustration_pages',
    'error_message',
    'stopped_at',
    'text_generation_started_at',
    'text_generation_completed_at',
    'illustration_generation_started_at',
    'illustration_generation_completed_at',
    'created_by_id',
])]
#[UsePolicy(StoryBookPolicy::class)]
#[ObservedBy([StoryBookObserver::class])]
class StoryBook extends Model
{
    use HasFactory, HasSlug, LogsActivity;

    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',

            'foundation' => 'array',
            'characters' => 'array',
            'world_bible' => 'array',
            'locations' => 'array',
            'factions' => 'array',
            'creatures' => 'array',
            'systems' => 'array',
            'timeline' => 'array',
            'story_structure' => 'array',
            'twists_and_foreshadowing' => 'array',
            'scene_plans' => 'array',
            'dialogue_plans' => 'array',
            'page_plan' => 'array',

            'current_step' => 'integer',
            'completed_steps_count' => 'integer',
            'current_illustration_page' => 'integer',
            'completed_illustration_pages' => 'integer',

            'stopped_at' => 'datetime',
            'text_generation_started_at' => 'datetime',
            'text_generation_completed_at' => 'datetime',
            'illustration_generation_started_at' => 'datetime',
            'illustration_generation_completed_at' => 'datetime',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function isLocked(): bool
    {
        return StoryBookHelper::isLocked($this->status);
    }

    public function textGenerationProgressPercentage(): int
    {
        return (int) round(
            min($this->completed_steps_count, StoryBookHelper::TEXT_GENERATION_STEP_COUNT)
            / StoryBookHelper::TEXT_GENERATION_STEP_COUNT
            * 100
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'sub_title',
                'datetime',
                'slug',
                'status',
                'audience_id',
                'story_book_type_id',
                'language_id',
                'illustration_type_id',
                'ai_brain_text_id',
                'ai_brain_illustration_id',
                'additional_information',
                'ai_prompt',

                'foundation',
                'characters',
                'world_bible',
                'locations',
                'factions',
                'creatures',
                'systems',
                'timeline',
                'story_structure',
                'twists_and_foreshadowing',
                'scene_plans',
                'dialogue_plans',
                'page_plan',
                'chapter_plan',

                'current_step',
                'completed_steps_count',
                'current_illustration_page',
                'completed_illustration_pages',
                'error_message',
                'stopped_at',
                'text_generation_started_at',
                'text_generation_completed_at',
                'illustration_generation_started_at',
                'illustration_generation_completed_at',
            ])
            ->useLogName('StoryBook')
            ->setDescriptionForEvent(fn (string $eventName) => "The record has been {$eventName}.")
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
            ->generateSlugsFrom(['title', 'sub_title'])
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(255)
            ->usingSuffixGenerator(fn () => Str::lower(Str::random(5)));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class);
    }

    public function aiBrainText(): BelongsTo
    {
        return $this->belongsTo(AiBrain::class, 'ai_brain_text_id');
    }

    public function aiBrainIllustration(): BelongsTo
    {
        return $this->belongsTo(AiBrain::class, 'ai_brain_illustration_id');
    }

    public function illustrationType(): BelongsTo
    {
        return $this->belongsTo(IllustrationType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_story_books');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function latestActivityLog(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')->latestOfMany();
    }

    public function storyBookType(): BelongsTo
    {
        return $this->belongsTo(StoryBookType::class);
    }

    public function storyBookPages(): HasMany
    {
        return $this->hasMany(StoryBookPage::class)->orderBy('no', 'asc');
    }
}
