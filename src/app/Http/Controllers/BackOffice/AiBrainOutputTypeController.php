<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Services\BackOffice\AiBrainOutputTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AiBrainOutputTypeController extends Controller
{
    protected AiBrainOutputTypeService $aiBrainOutputTypeService;

    public function __construct(AiBrainOutputTypeService $aiBrainOutputTypeService)
    {
        $this->aiBrainOutputTypeService = $aiBrainOutputTypeService;
    }

    public function index(Request $request): InertiaResponse
    {
        $aiBrainOutputType = $this->aiBrainOutputTypeService->new();

        Gate::authorize('viewAny', $aiBrainOutputType);

        return Inertia::render('back-office/ai-brain-output-types/Index', [
            'aiBrainOutputTypes' => $this->aiBrainOutputTypeService->search($request),
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $aiBrainOutputType = $this->aiBrainOutputTypeService->find($slug);

        Gate::authorize('view', $aiBrainOutputType);

        return Inertia::render('back-office/ai-brain-output-types/Details', [
            'aiBrainOutputType' => $aiBrainOutputType,
        ]);
    }
}