<?php

use App\Http\Controllers\AuthController;

// Backoffice
use App\Http\Controllers\BackOffice\MediaController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\BackOffice\GenreController;
use App\Http\Controllers\BackOffice\AudienceController;
use App\Http\Controllers\BackOffice\LanguageController;
use App\Http\Controllers\BackOffice\StoryBookTypeController;
use App\Http\Controllers\BackOffice\IllustrationTypeController;
use App\Http\Controllers\BackOffice\AiBrainController;
use App\Http\Controllers\BackOffice\AiPromptController;
use App\Http\Controllers\BackOffice\StoryBookController;
use App\Http\Controllers\BackOffice\SettingController;
use App\Http\Controllers\BackOffice\ActivityLogController;

//
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use romanzipp\QueueMonitor\Controllers\ShowQueueMonitorController;

Route::middleware('guest')->group(function () {
    Route::prefix('login')->group(function () {
        Route::get('/', [AuthController::class, 'loginForm'])->name('login'); // Default route name
        Route::post('/', [AuthController::class, 'login'])->name('login.submit')->middleware('throttle:3,1');
    });

    Route::prefix('register')->group(function () {
        Route::get('/', [AuthController::class, 'registerForm'])->name('register'); // Default route name
        Route::post('/', [AuthController::class, 'register'])->name('register.submit');
    });

    Route::prefix('forgot-password')->group(function () {
        Route::get('/', [AuthController::class, 'forgetPasswordForm'])->name('forgot-password');
        Route::post('/', [AuthController::class, 'forgotPassword'])->name('forgot-password.submit');
    });

    Route::prefix('password-reset')->group(function () {
        Route::get('{email}/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset'); // Default route name
        Route::post('{email}/{token}', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('verification')->group(function () {
        Route::get('notice', [AuthController::class, 'emailVerificationNotice'])->name('verification.notice');
        Route::post('resend', [AuthController::class, 'emailVerificationResend'])->name('verification.resend');
        Route::get('verification/{id}/{hash}', [AuthController::class, 'emailVerification'])->middleware('signed')->name('verification.verify'); // Default route name
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('auth-user')->name('auth-user.')->middleware('auth')->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('index', [AuthController::class, 'dashboard'])->name('index');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('index', [AuthController::class, 'profileIndex'])->name('index');
        Route::patch('update', [AuthController::class, 'profileUpdate'])->name('update');
    });

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('index', [AuthController::class, 'accountIndex'])->name('index');
        Route::patch('update', [AuthController::class, 'accountUpdate'])->name('update');
    });
});

Route::prefix('search')->name('search.')->group(function () {

    Route::middleware(['response.cache:3600,public,300,etag'])->group(function () {
        Route::get('per-pages', [SearchController::class, 'perPages'])->name('per-pages');
        Route::get('genders', [SearchController::class, 'genders'])->name('genders');
        Route::get('religions', [SearchController::class, 'religions'])->name('religions');
        Route::get('marital-statuses', [SearchController::class, 'maritalStatuses'])->name('marital-statuses');

        Route::get('activity-log-events', [SearchController::class, 'activityLogEvents'])->name('activity-log-events');
        Route::get('activity-log-subject-types', [SearchController::class, 'activityLogSubjectTypes'])->name('activity-log-subject-types');

        Route::get('story-book-continuities', [SearchController::class, 'storyBookContinuities'])->name('story-book-continuities');
        Route::get('story-book-statuses', [SearchController::class, 'storyBookStatuses'])->name('story-book-statuses');

        Route::get('user-permissions', [SearchController::class, 'userPermissions'])->name('user-permissions');
        Route::get('user-permissions-by-group', [SearchController::class, 'userPermissionsByGroup'])->name('user-permissions-by-group');
    });

    Route::middleware(['response.cache:60,public,30,etag'])->group(function () {
        Route::get('users', [SearchController::class, 'users'])->name('users');
        Route::get('genres', [SearchController::class, 'genres'])->name('genres');
        Route::get('audiences', [SearchController::class, 'audiences'])->name('audiences');
        Route::get('languages', [SearchController::class, 'languages'])->name('languages');
        Route::get('story-book-types', [SearchController::class, 'storyBookTypes'])->name('story-book-types');
        Route::get('illustration-types', [SearchController::class, 'illustrationTypes'])->name('illustration-types');
        Route::get('ai-brains', [SearchController::class, 'aiBrains'])->name('ai-brains');
        Route::get('ai-prompts', [SearchController::class, 'aiPrompts'])->name('ai-prompts');

        Route::get('medias', [SearchController::class, 'medias'])->name('medias');

        Route::get('user-permission/{slugOrId}', [SearchController::class, 'userPermission'])->name('user-permission');

        Route::get('genre/{slugOrId}', [SearchController::class, 'genre'])->name('genre');
        Route::get('audience/{slugOrId}', [SearchController::class, 'audience'])->name('audience');
        Route::get('language/{slugOrId}', [SearchController::class, 'language'])->name('language');
        Route::get('story-book-type/{slugOrId}', [SearchController::class, 'storyBookType'])->name('story-book-type');
        Route::get('illustration-type/{slugOrId}', [SearchController::class, 'illustrationType'])->name('illustration-type');
        Route::get('ai-brain/{slugOrId}', [SearchController::class, 'aiBrain'])->name('ai-brain');
        Route::get('ai-prompt/{slugOrId}', [SearchController::class, 'aiPrompt'])->name('ai-prompt');
    });

    Route::middleware(['response.cache:60,private,300,etag'])->get('user/{slugOrId}', [SearchController::class, 'user'])->name('user');
});

Route::prefix('back-office')->name('back-office.')->middleware(['auth', 'verified'])->group(function () {

    Route::prefix('medias')->name('medias.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::get('details/{slug}', [MediaController::class, 'details'])->name('details');
        Route::delete('delete/{slug}', [MediaController::class, 'delete'])->name('delete');

        Route::post('quick-save', [MediaController::class, 'quickSave'])->name('quick-save');
        Route::patch('quick-update/{slug}', [MediaController::class, 'quickUpdate'])->name('quick-update');
    });

    Route::prefix('genres')->name('genres.')->group(function () {
        Route::get('/', [GenreController::class, 'index'])->name('index');
        Route::get('create', [GenreController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [GenreController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [GenreController::class, 'details'])->name('details');

        Route::post('save', [GenreController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [GenreController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [GenreController::class, 'delete'])->name('delete');
    });

    Route::prefix('audiences')->name('audiences.')->group(function () {
        Route::get('/', [AudienceController::class, 'index'])->name('index');
        Route::get('create', [AudienceController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [AudienceController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [AudienceController::class, 'details'])->name('details');

        Route::post('save', [AudienceController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [AudienceController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [AudienceController::class, 'delete'])->name('delete');
    });

    Route::prefix('languages')->name('languages.')->group(function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::get('create', [LanguageController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [LanguageController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [LanguageController::class, 'details'])->name('details');

        Route::post('save', [LanguageController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [LanguageController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [LanguageController::class, 'delete'])->name('delete');
    });

    Route::prefix('story-book-types')->name('story-book-types.')->group(function () {
        Route::get('/', [StoryBookTypeController::class, 'index'])->name('index');
        Route::get('create', [StoryBookTypeController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [StoryBookTypeController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [StoryBookTypeController::class, 'details'])->name('details');

        Route::post('save', [StoryBookTypeController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [StoryBookTypeController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [StoryBookTypeController::class, 'delete'])->name('delete');
    });

    Route::prefix('illustration-types')->name('illustration-types.')->group(function () {
        Route::get('/', [IllustrationTypeController::class, 'index'])->name('index');
        Route::get('create', [IllustrationTypeController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [IllustrationTypeController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [IllustrationTypeController::class, 'details'])->name('details');

        Route::post('save', [IllustrationTypeController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [IllustrationTypeController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [IllustrationTypeController::class, 'delete'])->name('delete');
    });

    Route::prefix('ai-prompts')->name('ai-prompts.')->group(function () {
        Route::get('/', [AiPromptController::class, 'index'])->name('index');
        Route::get('edit/{slug}', [AiPromptController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [AiPromptController::class, 'details'])->name('details');

        Route::post('save', [AiPromptController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [AiPromptController::class, 'update'])->name('update');
    });


    Route::prefix('ai-brains')->name('ai-brains.')->group(function () {
        Route::get('/', [AiBrainController::class, 'index'])->name('index');
        Route::get('details/{slug}', [AiBrainController::class, 'details'])->name('details');

        Route::get('create', [AiBrainController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [AiBrainController::class, 'edit'])->name('edit');

        Route::post('save', [AiBrainController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [AiBrainController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [AiBrainController::class, 'delete'])->name('delete');
    });

    Route::prefix('story-books')->name('story-books.')->group(function () {
        Route::get('/', [StoryBookController::class, 'index'])->name('index');
        Route::post('save', [StoryBookController::class, 'save'])->name('save');

        Route::delete('delete/{slug}', [StoryBookController::class, 'delete'])->name('delete');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [UserController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [UserController::class, 'details'])->name('details');

        Route::post('save', [UserController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [UserController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [UserController::class, 'delete'])->name('delete');
        Route::patch('active/{slug}', [UserController::class, 'active'])->name('active');
        Route::patch('inactive/{slug}', [UserController::class, 'inactive'])->name('inactive');
    });

    Route::prefix('activity-logs')->name('activity-logs.')->middleware(['is.super.admin'])->group(function () {
        Route::get('index', [ActivityLogController::class, 'index'])->name('index');

        Route::get('details/{slug}', [ActivityLogController::class, 'details'])->name('details');
        Route::get('{modelSlug}/show-all/{recordSlug}', [ActivityLogController::class, 'indexForModel'])->name('show-all');

        Route::delete('delete/{slug}', [ActivityLogController::class, 'delete'])->name('delete');
    });

    Route::prefix('queue-monitor')->name('queue-monitor.')->middleware(['is.super.admin'])->group(function () {
        Route::get('/', ShowQueueMonitorController::class)->name('index');
    });

    Route::prefix('settings')->name('settings.')->middleware(['is.super.admin'])->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');

        Route::prefix('queue')->name('queue.')->group(function () {
            Route::get('start', [SettingController::class, 'queueStart'])->name('start');
            Route::get('restarted', [SettingController::class, 'queueReStart'])->name('restart');
            Route::get('clear', [SettingController::class, 'queueClear'])->name('clear');
            Route::get('flush', [SettingController::class, 'queueFlush'])->name('flush');

            Route::prefix('monitor')->name('monitor.')->group(function () {
                Route::get('stale', [SettingController::class, 'queueMonitorStale'])->name('stale');
                Route::get('purge', [SettingController::class, 'queueMonitorPurge'])->name('purge');
            });
        });

        Route::prefix('schedule')->name('schedule.')->group(function () {
            Route::get('start', [SettingController::class, 'scheduleStart'])->name('start');
            Route::get('stop', [SettingController::class, 'scheduleStop'])->name('stop');
        });

        Route::prefix('robots-txt')->name('robots-txt.')->group(function () {
            Route::get('edit', [SettingController::class, 'robotsTxtEdit'])->name('edit');
            Route::post('save', [SettingController::class, 'robotsTxtSave'])->name('save');
        });

        Route::prefix('ads-txt')->name('ads-txt.')->group(function () {
            Route::get('edit', [SettingController::class, 'adsTxtEdit'])->name('edit');
            Route::post('save', [SettingController::class, 'adsTxtSave'])->name('save');
        });
    });
});

Route::get('/', function () {
    return redirect()->route('home');
});

Route::middleware(['response.cache:30,public,15,etag'])->group(function () {

    Route::get('home', [PageController::class, 'home'])->name('home');
});
