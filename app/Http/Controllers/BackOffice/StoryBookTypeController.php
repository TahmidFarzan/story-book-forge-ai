<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookTypeRequest;
use App\Services\BackOffice\StoryBookTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StoryBookTypeController extends Controller
{
    protected StoryBookTypeService $storyBookTypeService;

    public function __construct(StoryBookTypeService $storyBookTypeService)
    {
        $this->storyBookTypeService = $storyBookTypeService;
    }

    public function index(Request $request): InertiaResponse
    {
        $storyBookType = $this->storyBookTypeService->new();
        Gate::authorize('viewAny', $storyBookType);

        $storyBookTypes = $this->storyBookTypeService->search($request);

        return Inertia::render('back-office/story-book-types/Index', [
            'storyBookTypes' => $storyBookTypes,
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $storyBookType = $this->storyBookTypeService->find($slug);

        Gate::authorize('view', $storyBookType);

        return Inertia::render('back-office/story-book-types/Details', [
            'storyBookType' => $storyBookType,
        ]);
    }

    public function create(): InertiaResponse
    {
        $storyBookType = $this->storyBookTypeService->new();
        Gate::authorize('create', $storyBookType);

        return Inertia::render('back-office/story-book-types/Create', [
            'storyBookType' => $storyBookType,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $storyBookType = $this->storyBookTypeService->find($slug);

        Gate::authorize('update', $storyBookType);

        return Inertia::render('back-office/story-book-types/Create', [
            'storyBookType' => $storyBookType,
        ]);
    }

    public function save(StoryBookTypeRequest $request): RedirectResponse
    {
        $storyBookType = $this->storyBookTypeService->new();
        Gate::authorize('create', $storyBookType);

        $result = $this->storyBookTypeService->save($request, $storyBookType);

        return to_route('back-office.story-book-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function update(StoryBookTypeRequest $request, string $slug): RedirectResponse
    {
        $storyBookType = $this->storyBookTypeService->find($slug);

        Gate::authorize('update', $storyBookType);

        $result = $this->storyBookTypeService->save($request, $storyBookType);

        return to_route('back-office.story-book-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $storyBookType = $this->storyBookTypeService->find($slug);

        Gate::authorize('delete', $storyBookType);

        $result = $this->storyBookTypeService->delete($storyBookType);

        return to_route('back-office.story-book-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}