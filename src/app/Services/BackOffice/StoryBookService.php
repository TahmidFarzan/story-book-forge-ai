<?php
namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookStep1;
use App\Http\Requests\StoryBookStep2;
use App\Http\Requests\StoryBookStep3;
use App\Http\Requests\StoryBookStep4;
use App\Http\Requests\StoryBookStep5;
use App\Http\Requests\StoryBookStep6;
use App\Http\Requests\StoryBookStep7;
use App\Http\Requests\StoryBookStep8;
use App\Http\Requests\StoryBookStep9;
use App\Http\Requests\StoryBookStep10;
use App\Http\Requests\StoryBookStep11;
use App\Http\Requests\StoryBookStep12;
use App\Http\Requests\StoryBookStep13;
use App\Http\Requests\StoryBookStep14;
use App\Http\Requests\StoryBookStep15;
use App\Http\Requests\StoryBookStep16;
use App\Models\AiBrain;
use App\Models\StoryBook;
use App\Models\StoryBookPage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoryBookService
{
    protected StoryBookGeneratorService $storyBookGeneratorService;

    public function __construct(StoryBookGeneratorService $storyBookGeneratorService)
    {
        $this->storyBookGeneratorService = $storyBookGeneratorService;
    }

    public function new (): StoryBook
    {
        return new StoryBook;
    }

    public function find(string $slug): StoryBook
    {
        return StoryBook::with([
            'language',
            'storyBookType',
            'audience',
            'genres',

            'storyBookPages' => fn($query) => $query->orderBy('no', 'asc'),
            'storyBookPages.illustrationImage',

            'createdBy',

            'activityLogs'   => fn($query)   => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = StoryBook::query();

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
                'title',
                'sub_title',
            ], 'like', $likeSearch);
        }

        if ($request->filled('genre_id')) {
            $query->whereHas(
                'genres',
                fn($query) => $query->where('genres.id', $request->input('genre_id'))
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function step1Prompt(StoryBookStep1 $request, StoryBook $storyBook): array
    {
        $isNew       = empty($storyBook->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {

            $apiResponse = $this->storyBookGeneratorService->step1Prompt($request, $storyBook);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($request, $apiResponse, $storyBook, $isNew) {
                $storyBook->title      = $apiResponse->title;
                $storyBook->sub_title  = $apiResponse->subtitle;
                $storyBook->foundation = $apiResponse->foundation;

                $storyBook->audience_id        = $request->input('audience_id');
                $storyBook->story_book_type_id = $request->input('story_book_type_id');
                $storyBook->language_id        = $request->input('language_id');

                $storyBook->additional_information = $request->input('additional_information');

                $storyBook->status = StoryBookHelper::STATUS_ONGOING;

                if ($isNew) {
                    $storyBook->datetime      = now();
                    $storyBook->created_by_id = Auth::id();
                }

                $storyBook->save();

                if ($request->has('genre_ids')) {
                    $storyBook->genres()->sync((array) $request->input('genre_ids', []));
                }

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => $isNew
                    ? 'Story created successfully.'
                    : 'Story updated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error("Failed to {$statusEvent} story.", [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step2Prompt(StoryBookStep2 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step2Prompt($request, $storyBook);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->characters = $apiResponse;
                $storyBook->status     = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story characters generate successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story characters', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step3Prompt(StoryBookStep3 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step3Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->world_bible = $apiResponse;
                $storyBook->status      = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story world bible generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story world bible', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step4Prompt(StoryBookStep4 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step4Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->locations = $apiResponse;
                $storyBook->status    = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story locations generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story locations', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step5Prompt(StoryBookStep5 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step5Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->factions = $apiResponse;
                $storyBook->status   = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story factions generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story factions', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step6Prompt(StoryBookStep6 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step6Prompt($request, $storyBook);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->creatures = $apiResponse;
                $storyBook->status    = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story creatures generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story creatures', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step7Prompt(StoryBookStep7 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step7Prompt($request, $storyBook);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->systems = $apiResponse;
                $storyBook->status  = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story systems generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story systems', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step8Prompt(StoryBookStep8 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step8Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->timeline = $apiResponse;
                $storyBook->status   = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story timeline generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story timeline', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step9Prompt(StoryBookStep9 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step9Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->story_structure = $apiResponse;
                $storyBook->status          = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story structure generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story structure', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step10Prompt(StoryBookStep10 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step10Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->twists_and_foreshadowing = $apiResponse;
                $storyBook->status                   = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story twists and foreshadowing generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story twists and foreshadowing', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step11Prompt(StoryBookStep11 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step11Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->scene_plans = $apiResponse;
                $storyBook->status      = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story scene plan generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story scene plan', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step12Prompt(StoryBookStep12 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step12Prompt($request, $storyBook);
            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->dialogue_plans = $apiResponse;
                $storyBook->status         = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story dialogue plan generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story dialogue plan', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step13Prompt(StoryBookStep13 $request, StoryBook $storyBook): array
    {
        try {
            $apiResponse = $this->storyBookGeneratorService->step13Prompt($request, $storyBook);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->page_plan = $apiResponse;
                $storyBook->status    = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story page plan generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story page plan', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step14Prompt(StoryBookStep14 $request, StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService->step14Prompt($request, $storyBook);
    }

    public function step15Prompt(StoryBookStep15 $request, StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService->step15Prompt($request, $storyBook);
    }

    public function step16Prompt(StoryBookStep16 $request, StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService->step16Prompt($request, $storyBook);
    }

    public function delete(StoryBook $storyBook): array
    {

        try {

            DB::transaction(function () use ($storyBook) {
                $storyBook->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Story deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete story. Please try again.',
            ];
        }
    }
}
