<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryBookStep2;
use App\Http\Requests\StoryBookStep6;
use App\Http\Requests\StoryBookStep12;
use App\Http\Requests\StoryBookStep5;
use App\Http\Requests\StoryBookStep1;
use App\Http\Requests\StoryBookStep4;
use App\Http\Requests\StoryBookStep13;
use App\Http\Requests\StoryBookStep11;
use App\Http\Requests\StoryBookStep14;
use App\Http\Requests\StoryBookStep15;
use App\Http\Requests\StoryBookStep16;
use App\Http\Requests\StoryBookStep9;
use App\Http\Requests\StoryBookStep7;
use App\Http\Requests\StoryBookStep8;
use App\Http\Requests\StoryBookStep10;
use App\Http\Requests\StoryBookStep3;
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

    public function createStep1Foundation(StoryBookStep1 $request): RedirectResponse
    {
        $storyBook = $this->storyBookService->new();
        Gate::authorize('create', $storyBook);

        $result = $this->storyBookService->step1Prompt($request, $storyBook);

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

    public function generateStep1(StoryBookStep1 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step1Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep2(StoryBookStep2 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step2Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep3(StoryBookStep3 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step3Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep4(StoryBookStep4 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step4Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep5(StoryBookStep5 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step5Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep6(StoryBookStep6 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step6Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep7(StoryBookStep7 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step7Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep8(StoryBookStep8 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step8Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep9(StoryBookStep9 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step9Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep10(StoryBookStep10 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step10Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep11(StoryBookStep11 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step11Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep12(StoryBookStep12 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step12Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep13(StoryBookStep13 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step13Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep14(StoryBookStep14 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step14Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep15(StoryBookStep15 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step15Prompt($request, $storyBook);

        return to_route('back-office.story-books.edit', ['slug' => $storyBook?->slug])->with('flash_message', [
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function generateStep16(StoryBookStep16 $request, string $slug): RedirectResponse
    {
        $storyBook = $this->storyBookService->find($slug);
        Gate::authorize('update', $storyBook);

        $result = $this->storyBookService->step16Prompt($request, $storyBook);

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
