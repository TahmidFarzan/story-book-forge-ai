<?php
namespace App\Services\BackOffice;

use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LanguageService
{
    public function new (): Language
    {
        return new Language;
    }

    public function find(string $slug): Language
    {
        return Language::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Language::query();

        if ($request->filled('created_by_id')) {
            $query->where('created_by_id', $request->input('created_by_id'));
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $date = is_string($date) ? new \DateTime($date) : $date;
            $query->whereDate('created_at', '<=', $date);
        }

        if ($request->filled('search')) {
            $search     = $request->input('search');
            $likeSearch = "%{$search}%";

            $query->whereAny([
                'name',
                'brief',
            ], 'like', $likeSearch);
        }

        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function save(LanguageRequest $request, Language $language): array
    {
        $isNew       = empty($language->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {

            DB::transaction(function () use ($request, $language, $isNew) {
                $language->name               = $request->input('name');
                $language->brief              = $request->input('brief');
                $language->created_by_id      = $isNew ? Auth::id() : $language->created_by_id;

                $language->save();
            });

            return [
                'status'  => 'success',
                'message' => $isNew ? 'Language created successfully.' : 'Language updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} language.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save language. Please try again.',
            ];
        }
    }

    public function delete(Language $language): array
    {

        try {

            DB::transaction(function () use ($language) {
                $language->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Language deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Language delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete language. Please try again.',
            ];
        }
    }
}