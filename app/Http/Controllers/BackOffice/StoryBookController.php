<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookRequest;
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

    public function save(StoryBookRequest $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->save($request, $storyBook);

        return to_route('back-office.story-books.index')->with('flash_message', [
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
