<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\IllustrationTypeRequest;
use App\Services\BackOffice\IllustrationTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class IllustrationTypeController extends Controller
{
    protected IllustrationTypeService $illustrationTypeService;

    public function __construct(IllustrationTypeService $illustrationTypeService)
    {
        $this->illustrationTypeService = $illustrationTypeService;
    }

    public function index(Request $request): InertiaResponse
    {
        $illustrationType = $this->illustrationTypeService->new();
        Gate::authorize('viewAny', $illustrationType);

        $illustrationTypes = $this->illustrationTypeService->search($request);

        return Inertia::render('back-office/illustration-types/Index', [
            'illustrationTypes' => $illustrationTypes,
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $illustrationType = $this->illustrationTypeService->find($slug);

        Gate::authorize('view', $illustrationType);

        return Inertia::render('back-office/illustration-types/Details', [
            'illustrationType' => $illustrationType,
        ]);
    }

    public function create(): InertiaResponse
    {
        $illustrationType = $this->illustrationTypeService->new();
        Gate::authorize('create', $illustrationType);

        return Inertia::render('back-office/illustration-types/Create', [
            'illustrationType' => $illustrationType,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $illustrationType = $this->illustrationTypeService->find($slug);

        Gate::authorize('update', $illustrationType);

        return Inertia::render('back-office/illustration-types/Create', [
            'illustrationType' => $illustrationType,
        ]);
    }

    public function save(IllustrationTypeRequest $request): RedirectResponse
    {
        $illustrationType = $this->illustrationTypeService->new();
        Gate::authorize('create', $illustrationType);

        $result = $this->illustrationTypeService->save($request, $illustrationType);

        return to_route('back-office.illustration-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function update(IllustrationTypeRequest $request, string $slug): RedirectResponse
    {
        $illustrationType = $this->illustrationTypeService->find($slug);

        Gate::authorize('update', $illustrationType);

        $result = $this->illustrationTypeService->save($request, $illustrationType);

        return to_route('back-office.illustration-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $illustrationType = $this->illustrationTypeService->find($slug);

        Gate::authorize('delete', $illustrationType);

        $result = $this->illustrationTypeService->delete($illustrationType);

        return to_route('back-office.illustration-types.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}