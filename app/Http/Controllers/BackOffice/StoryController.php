<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryRequest;
use App\Services\BackOffice\StoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StoryController extends Controller
{
    protected StoryService $storyService;

    public function __construct(StoryService $storyService)
    {
        $this->storyService = $storyService;
    }

    public function index(Request $request): InertiaResponse
    {
        $story = $this->storyService->new();

        Gate::authorize('viewAny', $story);

        return Inertia::render('back-office/stories/Index', [
            'stories' => $this->storyService->search($request),
        ]);
    }

    public function save(StoryRequest $request): RedirectResponse
    {
        $story = $this->storyService->new();
        Gate::authorize('create', $story);

        $result = $this->storyService->save($request, $story);

        return to_route('back-office.stories.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $user = $this->storyService->find($slug);

        Gate::authorize('delete', $user);

        $result = $this->storyService->delete($user);

        return to_route('back-office.stories.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}
