<?php
namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Services\BackOffice\GenreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class GenreController extends Controller
{
    protected GenreService $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    public function index(Request $request): InertiaResponse
    {
        $genre = $this->genreService->new();
        Gate::authorize('viewAny', $genre);

        $genres = $this->genreService->search($request);

        return Inertia::render('back-office/genres/Index', [
            'genres' => $genres,
        ]);
    }

    public function details(string $slug): InertiaResponse
    {
        $genre = $this->genreService->find($slug);

        Gate::authorize('view', $genre);

        return Inertia::render('back-office/genres/Details', [
            'genre' => $genre,
        ]);
    }

    public function create(): InertiaResponse
    {
        $genre = $this->genreService->new();
        Gate::authorize('create', $genre);

        return Inertia::render('back-office/genres/Create', [
            'genre' => $genre,
        ]);
    }

    public function edit(string $slug): InertiaResponse
    {
        $genre = $this->genreService->find($slug);

        Gate::authorize('update', $genre);

        return Inertia::render('back-office/genres/Create', [
            'genre' => $genre,
        ]);
    }

    public function save(GenreRequest $request): RedirectResponse
    {
        $genre = $this->genreService->new();
        Gate::authorize('create', $genre);

        $result = $this->genreService->save($request, $genre);

        return to_route('back-office.genres.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function update(GenreRequest $request, string $slug): RedirectResponse
    {
        $genre = $this->genreService->find($slug);

        Gate::authorize('update', $genre);

        $result = $this->genreService->save($request, $genre);

        return to_route('back-office.genres.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    public function delete(string $slug): RedirectResponse
    {
        $genre = $this->genreService->find($slug);

        Gate::authorize('delete', $genre);

        $result = $this->genreService->delete($genre);

        return to_route('back-office.genres.index')->with('flash_message', [
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }
}
