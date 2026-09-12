<?php
namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
        $this->middleware(['auth', 'verified'])->only(['user']);
    }

    public function perPages(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->perPages($request)
        );
    }

    public function genders(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->genders($request)
        );
    }

    public function religions(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->religions($request)
        );
    }

    public function maritalStatuses(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->maritalStatuses($request)
        );
    }

    public function activityLogEvents(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->activityLogEvents($request)
        );
    }

    public function activityLogSubjectTypes(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->activityLogSubjectTypes($request)
        );
    }

    public function storyBookContinuities(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->storyBookContinuities($request)
        );
    }

    public function storyBookStatuses(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->storyBookStatuses($request)
        );
    }

    public function users(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->users($request)
        );
    }

    public function genres(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->genres($request)
        );
    }

    public function audiences(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->audiences($request)
        );
    }

    public function aiBrains(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->aiBrains($request)
        );
    }

    public function aiPrompts(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->aiPrompts($request)
        );
    }

    public function languages(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->languages($request)
        );
    }

    public function storyTypes(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->storyTypes($request)
        );
    }

    public function illustrationTypes(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->illustrationTypes($request)
        );
    }

    public function userPermissions(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->userPermissions($request)
        );
    }

    public function userPermissionsByGroup(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->userPermissionsByGroup($request)
        );
    }

    public function medias(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->medias($request)
        );
    }

    public function userPermission(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->userPermission($slugOrId)
        );
    }

    public function genre(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->genre($slugOrId)
        );
    }

    public function audience(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->audience($slugOrId)
        );
    }

    public function aiBrain(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->aiBrain($slugOrId)
        );
    }

    public function aiPrompt(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->aiPrompt($slugOrId)
        );
    }

    public function language(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->language($slugOrId)
        );
    }

    public function storyType(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->storyType($slugOrId)
        );
    }

    public function illustrationType(string | int $slugOrId): JsonResponse
    {
        return response()->json(
            $this->searchService->illustrationType($slugOrId)
        );
    }
}
