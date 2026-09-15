<?php

namespace App\Models;

use App\Observers\StoryBookObserver;
use App\Policies\StoryBookPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    "audience_id",
    "story_book_type_id",
    'language_id',
    'ai_prompt',
    'received_inputs',
    'plot',
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
    'created_by_id',
])]
#[UsePolicy(StoryBookPolicy::class)]
#[ObservedBy([StoryBookObserver::class])]
class StoryBook extends Model
{
    use HasFactory, LogsActivity, HasSlug;

    protected $appends = [];

    protected function casts(): array
    {
        return [
            'received_inputs'   => 'array',
            'datetime'   => 'datetime',

            'plot' => 'array',
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

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
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
                "audience_id",
                "story_book_type_id",
                'language_id',
                'ai_prompt',
                'received_inputs',
                'plot',
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
            ])
            ->useLogName('StoryBook')
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
            ->generateSlugsFrom(["title", "sub_title"])
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(255)
            ->usingSuffixGenerator(fn() => Str::lower(Str::random(5)));
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
}
