<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryTypeRequest;
use App\Services\BackOffice\StoryTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StoryTypeController extends Controller
{
    protected StoryTypeService $storyTypeService;

    public function __construct(StoryTypeService $storyTypeService)
    {
        $this->storyTypeService = $storyTypeService;
    }

    public function index(Request $request): InertiaResponse
    {
        $storyType = $this->storyTypeService->new();
        Gate::authorize('viewAny', $storyType);

        $storyTypes = $this->storyTypeService->search($request);

        return Inertia::render('back-office/story-types/Index', [
            'storyTypes' => $storyTypes,
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $storyType = $this->storyTypeService->find($slug);

        Gate::authorize('view', $storyType);

        return Inertia::render('back-office/story-types/Details', [
            'storyType' => $storyType,
        ]);
    }

    public function create(): InertiaResponse
    {
        $storyType = $this->storyTypeService->new();
        Gate::authorize('create', $storyType);

        return Inertia::render('back-office/story-types/Create', [
            'storyType' => $storyType,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $storyType = $this->storyTypeService->find($slug);

        Gate::authorize('update', $storyType);

        return Inertia::render('back-office/story-types/Create', [
            'storyType' => $storyType,
        ]);
    }

    public function save(StoryTypeRequest $request): RedirectResponse
    {
        $storyType = $this->storyTypeService->new();
        Gate::authorize('create', $storyType);

        $result = $this->storyTypeService->save($request, $storyType);

        return to_route('back-office.story-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function update(StoryTypeRequest $request, string $slug): RedirectResponse
    {
        $storyType = $this->storyTypeService->find($slug);

        Gate::authorize('update', $storyType);

        $result = $this->storyTypeService->save($request, $storyType);

        return to_route('back-office.story-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $storyType = $this->storyTypeService->find($slug);

        Gate::authorize('delete', $storyType);

        $result = $this->storyTypeService->delete($storyType);

        return to_route('back-office.story-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}