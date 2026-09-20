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
use App\Http\Requests\StoryBookStep14PageNarrationRequest;
use App\Http\Requests\StoryBookStep15IllustrationPlanningRequest;
use App\Http\Requests\StoryBookStep16IllustrationGenerationRequest;
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

        $result = $this->storyBookService->generateStep1($request, $storyBook);

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

    public function generateStep1(StoryBookStep1FoundationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep1($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep2(StoryBookStep2CharactersRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep2($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep3(StoryBookStep3WorldVibeRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep3($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep4(StoryBookStep4LocationsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep4($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep5(StoryBookStep5FactionsRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep5($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep6(StoryBookStep6CreatureRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep6($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep7(StoryBookStep7SystemRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep7($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep8(StoryBookStep8TimelineRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep8($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep9(StoryBookStep9StoryStructureRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep9($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep10(StoryBookStep10TwistsAndForeshadowingRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep10($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep11(StoryBookStep11ScenePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep11($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep12(StoryBookStep12DialoguePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep12($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep13(StoryBookStep13PagePlanRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep13($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep14(StoryBookStep14PageNarrationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep14($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep15(StoryBookStep15IllustrationPlanningRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep15($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep16(StoryBookStep16IllustrationGenerationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateStep16($request, $storyBook);

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
