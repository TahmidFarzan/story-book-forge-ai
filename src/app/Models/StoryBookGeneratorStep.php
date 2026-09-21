<?php

namespace App\Models;

use App\Policies\StoryBookGeneratorStepPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
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

#[Table('story_book_generator_steps')]
#[Fillable([
    'name', 'slug', 'depend_on_step_ids', 'previous_step_id', 'next_step_id', 'ai_prompt_id',
])]
#[UsePolicy(StoryBookGeneratorStepPolicy::class)]
class StoryBookGeneratorStep extends Model
{
    use HasFactory, HasSlug, LogsActivity;

    protected $appends = [];

    protected function casts(): array
    {
        return [
            'depend_on_step_ids' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name', 'depend_on_step_ids', 'previous_step_id', 'next_step_id', 'ai_prompt_id', 'slug',
            ])
            ->useLogName('Story Book Generator Step')
            ->setDescriptionForEvent(fn (string $eventName) => "The record has been {$eventName}.")
            ->logOnlyDirty()
            ->logExcept([
                'id',
                'created_at',
            ])
            ->dontLogEmptyChanges();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->saveSlugsTo('slug')
            ->generateSlugsFrom('name')
            ->doNotGenerateSlugsOnUpdate()
            ->slugsShouldBeNoLongerThan(255)
            ->usingSuffixGenerator(fn () => Str::lower(Str::random(5)));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function dependOnSteps(): Collection
    {
        $ids = $this->depend_on_step_ids ?? [];

        if (count($ids) === 0) {
            return new Collection;
        }

        return StoryBookGeneratorStep::whereIn('id', $ids)
            ->orderBy('id')
            ->get();
    }

    public function aiPrompt(): BelongsTo
    {
        return $this->belongsTo(AiPrompt::class, 'ai_prompt_id');
    }

    public function previousStep(): BelongsTo
    {
        return $this->belongsTo(StoryBookGeneratorStep::class, 'previous_step_id');
    }

    public function nextStep(): BelongsTo
    {
        return $this->belongsTo(StoryBookGeneratorStep::class, 'next_step_id');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function latestActivityLog(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')->latestOfMany();
    }
}
