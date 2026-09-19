<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookStep2CharactersRequest;
use App\Http\Requests\StoryBookStep6CreatureRequest;
use App\Http\Requests\StoryBookStep12DialoguePlanRequest;
use App\Http\Requests\StoryBookStep5FactionsRequest;
use App\Http\Requests\StoryBookStep1FoundationRequest;
use App\Http\Requests\StoryBookStep4LocationsRequest;
use App\Http\Requests\StoryBookStep13PagePlanRequest;
use App\Http\Requests\StoryBookStep11ScenePlanRequest;
use App\Http\Requests\StoryBookStep14_1PageNarrationRequest;
use App\Http\Requests\StoryBookStep14_2IllustrationPlanningRequest;
use App\Http\Requests\StoryBookStep14_3IllustrationGenerationRequest;
use App\Http\Requests\StoryBookStep9StoryStructureRequest;
use App\Http\Requests\StoryBookStep7SystemRequest;
use App\Http\Requests\StoryBookStep8TimelineRequest;
use App\Http\Requests\StoryBookStep10TwistsAndForeshadowingRequest;
use App\Http\Requests\StoryBookStep3WorldVibeRequest;
use App\Services\BackOffice\StoryBookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StoryBookController extends Controller
{
    protected StoryBookService $storyBookService;

    public function __construct(StoryBookService $storyBookService)
    {
        $this->storyBookService = $storyBookService;
    }

    public function index(Request $request): InertiaResponse
    {
        $storyBook = $this->storyBookService->new();

        Gate::authorize('viewAny', $storyBook);

        return Inertia::render('back-office/story-books/Index', [
            'storyBooks' => $this->storyBookService->search($request),
        ]);
    }

    public function create(): InertiaResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('view', $storyBook);

        return Inertia::render('back-office/story-books/Create', [
            'storyBook' => $storyBook,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        return Inertia::render('back-office/story-books/Create', [
            'storyBook' => $storyBook,
        ]);
    }

    public function createStep1Foundation(StoryBookStep1FoundationRequest $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->generateStep1Foundation($request, $storyBook);

        if ($result['story_book']?->slug) {
            return to_route('back-office.story-books.edit', ['slug' => $result['story_book']?->slug])->with('flash_message', [
                'message' => $result['message'],
                'status' => $result['status'],
            ]);
        }

        return to_route('back-office.story-books.index')->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep1Foundation(StoryBookStep1FoundationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep1Foundation($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep2Characters(StoryBookStep2CharactersRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep2Characters($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep3WorldVibe(StoryBookStep3WorldVibeRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep3WorldVibe($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep4Locations(StoryBookStep4LocationsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep4Locations($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep5Factions(StoryBookStep5FactionsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep5Factions($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep6Creature(StoryBookStep6CreatureRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep6Creature($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep7System(StoryBookStep7SystemRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep7System($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep8Timeline(StoryBookStep8TimelineRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep8Timeline($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep9StoryStructure(StoryBookStep9StoryStructureRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep9StoryStructure($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep10TwistsAndForeshadowing(StoryBookStep10TwistsAndForeshadowingRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep10TwistsAndForeshadowing($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep11ScenePlan(StoryBookStep11ScenePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep11ScenePlan($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep12DialoguePlan(StoryBookStep12DialoguePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep12DialoguePlan($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep13PagePlan(StoryBookStep13PagePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep13PagePlan($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep14_1PageNarration(StoryBookStep14_1PageNarrationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep14_1PageNarration($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep14_2IllustrationPlanning(StoryBookStep14_2IllustrationPlanningRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep14_2IllustrationPlanning($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep14_3Illustration(StoryBookStep14_3IllustrationGenerationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep14_3Illustration($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('delete', $storyBook);

        $result = $this->storyBookService->delete($storyBook);

        return to_route('back-office.story-books.index')->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }
}
