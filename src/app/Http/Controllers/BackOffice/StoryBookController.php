<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookCharactersRequest;
use App\Http\Requests\StoryBookCompleteStoryBookRequest;
use App\Http\Requests\StoryBookCreaturesRequest;
use App\Http\Requests\StoryBookDialoguePlanRequest;
use App\Http\Requests\StoryBookFactionsRequest;
use App\Http\Requests\StoryBookFoundationRequest;
use App\Http\Requests\StoryBookLocationsRequest;
use App\Http\Requests\StoryBookPagePlanRequest;
use App\Http\Requests\StoryBookScenePlanRequest;
use App\Http\Requests\StoryBookStoryStructureRequest;
use App\Http\Requests\StoryBookSystemsRequest;
use App\Http\Requests\StoryBookTimelineRequest;
use App\Http\Requests\StoryBookTwistsAndForeshadowingRequest;
use App\Http\Requests\StoryBookWorldVibeRequest;
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

    public function createFoundation(StoryBookFoundationRequest $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->generateFoundation($request, $storyBook);

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

    public function generateFoundation(StoryBookFoundationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateFoundation($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateCharacters(StoryBookCharactersRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateCharacters($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateWorldVibe(StoryBookWorldVibeRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateWorldBible($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateLocations(StoryBookLocationsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateLocations($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateFactions(StoryBookFactionsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateFactions($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateCreature(StoryBookCreaturesRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateCreatures($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateSystem(StoryBookSystemsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateSystems($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateTimeline(StoryBookTimelineRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateTimeline($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStoryStructure(StoryBookStoryStructureRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStoryStructure($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateTwistsAndForeshadowing(StoryBookTwistsAndForeshadowingRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateTwistsAndForeshadowing($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateScenePlan(StoryBookScenePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateScenePlan($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateDialoguePlan(StoryBookDialoguePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateDialoguePlan($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generatePagePlan(StoryBookPagePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generatePagePlan($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateCompleteStoryBook(StoryBookCompleteStoryBookRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateCompleteStoryBook($request, $storyBook);

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
