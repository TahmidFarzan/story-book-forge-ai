<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AudienceRequest;
use App\Services\BackOffice\AudienceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AudienceController extends Controller
{
    protected AudienceService $audienceService;

    public function __construct(AudienceService $audienceService)
    {
        $this->audienceService = $audienceService;
    }

    public function index(Request $request): InertiaResponse
    {
        $audience = $this->audienceService->new();
        Gate::authorize('viewAny', $audience);

        $audiences = $this->audienceService->search($request);

        return Inertia::render('back-office/audiences/Index', [
            'audiences' => $audiences,
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $audience = $this->audienceService->find($slug);

        Gate::authorize('view', $audience);

        return Inertia::render('back-office/audiences/Details', [
            'audience' => $audience,
        ]);
    }

    public function create(): InertiaResponse
    {
        $audience = $this->audienceService->new();
        Gate::authorize('create', $audience);

        return Inertia::render('back-office/audiences/Create', [
            'audience' => $audience,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $audience = $this->audienceService->find($slug);

        Gate::authorize('update', $audience);

        return Inertia::render('back-office/audiences/Create', [
            'audience' => $audience,
        ]);
    }

    public function save(AudienceRequest $request): RedirectResponse
    {
        $audience = $this->audienceService->new();
        Gate::authorize('create', $audience);

        $result = $this->audienceService->save($request, $audience);

        return to_route('back-office.audiences.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function update(AudienceRequest $request, string $slug): RedirectResponse
    {
        $audience = $this->audienceService->find($slug);

        Gate::authorize('update', $audience);

        $result = $this->audienceService->save($request, $audience);

        return to_route('back-office.audiences.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $audience = $this->audienceService->find($slug);

        Gate::authorize('delete', $audience);

        $result = $this->audienceService->delete($audience);

        return to_route('back-office.audiences.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}
