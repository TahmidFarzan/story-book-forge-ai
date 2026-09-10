<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Services\BackOffice\LanguageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LanguageController extends Controller
{
    protected LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function index(Request $request): InertiaResponse
    {
        $language = $this->languageService->new();
        Gate::authorize('viewAny', $language);

        $languages = $this->languageService->search($request);

        return Inertia::render('back-office/languages/Index', [
            'languages' => $languages,
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $language = $this->languageService->find($slug);

        Gate::authorize('view', $language);

        return Inertia::render('back-office/languages/Details', [
            'language' => $language,
        ]);
    }

    public function create(): InertiaResponse
    {
        $language = $this->languageService->new();
        Gate::authorize('create', $language);

        return Inertia::render('back-office/languages/Create', [
            'language' => $language,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $language = $this->languageService->find($slug);

        Gate::authorize('update', $language);

        return Inertia::render('back-office/languages/Create', [
            'language' => $language,
        ]);
    }

    public function save(LanguageRequest $request): RedirectResponse
    {
        $language = $this->languageService->new();
        Gate::authorize('create', $language);

        $result = $this->languageService->save($request, $language);

        return to_route('back-office.languages.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function update(LanguageRequest $request, string $slug): RedirectResponse
    {
        $language = $this->languageService->find($slug);

        Gate::authorize('update', $language);

        $result = $this->languageService->save($request, $language);

        return to_route('back-office.languages.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $language = $this->languageService->find($slug);

        Gate::authorize('delete', $language);

        $result = $this->languageService->delete($language);

        return to_route('back-office.languages.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}