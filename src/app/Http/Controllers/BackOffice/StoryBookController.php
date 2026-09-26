<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookGenerate;
use App\Http\Requests\StoryBookIllustrationStart;
use App\Services\BackOffice\StoryBookService;
use Illuminate\Http\JsonResponse;
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
            'progress'  => $this->storyBookService->progress($storyBook),
        ]);
    }

    public function startTextGeneration(StoryBookGenerate $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();

        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->startTextGeneration($request);

        if ($result['story_book']?->slug) {
            return to_route('back-office.story-books.edit', ['slug' => $result['story_book']->slug])->with('flash_message', [
                'message' => $result['message'],
                'status'  => $result['status'],
            ]);
        }

        return to_route('back-office.story-books.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function resumeTextGeneration(string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->resumeTextGeneration($storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function stopTextGeneration(string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->stopTextGeneration($storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function startIllustrationGeneration(StoryBookIllustrationStart $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->startIllustrationGeneration($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function stopIllustrationGeneration(string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->stopIllustrationGeneration($storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function generationStatus(string $slug): JsonResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('view', $storyBook);

        return response()->json([
            'story_book' => $storyBook,
            'progress'   => $this->storyBookService->progress($storyBook),
        ]);
    }

    public function save(StoryBookGenerate $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->save($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);

        Gate::authorize('delete', $storyBook);

        $result = $this->storyBookService->delete($storyBook);

        return to_route('back-office.story-books.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}
