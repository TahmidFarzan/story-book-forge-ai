<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookFoundationRequest;
use App\Http\Requests\StoryBookCharactersRequest;
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

    public function generateFoundation(StoryBookFoundationRequest $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->generateFoundation($request, $storyBook);

        if ($result['story_book']?->slug) {
            return to_route('back-office.story-books.edit', ["slug" => $result['story_book']?->slug])->with('flash_message', [
                'message' => $result['message'],
                'status'  => $result['status'],
            ]);
        }
        return to_route('back-office.story-books.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function regenerateFoundation(StoryBookFoundationRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateFoundation($request, $storyBook);

        return to_route('back-office.story-books.edit', ["slug" => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function generateCharacters(StoryBookCharactersRequest $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->generateCharacters($request, $storyBook);

        if ($result['story_book']?->slug) {
            return to_route('back-office.story-books.edit', ["slug" => $result['story_book']?->slug])->with('flash_message', [
                'message' => $result['message'],
                'status'  => $result['status'],
            ]);
        }
        return to_route('back-office.story-books.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function regenerateCharacters(StoryBookCharactersRequest $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->generateCharacters($request, $storyBook);

        return to_route('back-office.story-books.edit', ["slug" => $slug])->with('flash_message', [
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
