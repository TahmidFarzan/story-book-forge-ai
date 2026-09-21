<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Services\BackOffice\StoryBookGeneratorStepService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StoryBookGeneratorStepController extends Controller
{
    protected StoryBookGeneratorStepService $storyBookGeneratorStepService;

    public function __construct(StoryBookGeneratorStepService $storyBookGeneratorStepService)
    {
        $this->storyBookGeneratorStepService = $storyBookGeneratorStepService;
    }

    public function index(Request $request): InertiaResponse
    {
        $storyBookGeneratorStep = $this->storyBookGeneratorStepService->new();

        Gate::authorize('viewAny', $storyBookGeneratorStep);

        return Inertia::render('back-office/story-book-generator-steps/Index', [
            'storyBookGeneratorSteps' => $this->storyBookGeneratorStepService->search($request),
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $storyBookGeneratorStep = $this->storyBookGeneratorStepService->find($slug);

        Gate::authorize('view', $storyBookGeneratorStep);

        return Inertia::render('back-office/story-book-generator-steps/Details', [
            'storyBookGeneratorStep' => $storyBookGeneratorStep,
            'dependOnSteps' => $storyBookGeneratorStep->dependOnSteps(),
        ]);
    }
}
