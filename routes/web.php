<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DealProposalController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\DealFollowUpController;
use App\Http\Controllers\DealActivityController;




/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    | Dashboard
    */
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
        ->name('dashboard');

    /*
    | Deals (Negócios)
    */
    Route::get('/deals', [DealController::class, 'index'])
        ->name('deals.index');

    Route::get('/deals/create', [DealController::class, 'create'])
        ->name('deals.create');

    Route::post('/deals', [DealController::class, 'store'])
        ->name('deals.store');

    Route::get('/deals/{deal}', [DealController::class, 'show'])
        ->name('deals.show');

    Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage'])
        ->name('deals.stage');

        /*
| Deal Follow-ups
*/
Route::post('/deals/{deal}/follow-ups', [DealFollowUpController::class, 'store'])
    ->name('deals.followups.store');

    Route::get('/follow-ups/templates', [DealFollowUpController::class, 'templates'])
    ->name('followups.templates');

    Route::patch('/follow-ups/{followUp}/cancel', [DealFollowUpController::class, 'cancel'])
    ->name('followups.cancel');


    /*
| Deal Activities
*/
Route::post('/deals/{deal}/activities', [DealActivityController::class, 'store'])
    ->name('deals.activities.store');

Route::patch('/activities/{activity}/complete', [DealActivityController::class, 'complete'])
    ->name('activities.complete');

        /*
| Product
*/

    Route::post('/deals/{deal}/products', [DealController::class, 'attachProduct'])
    ->name('deals.products.attach');



    /*
    | Deal Proposals
    */
    Route::post('/deals/{deal}/proposals', [DealProposalController::class, 'store'])
        ->name('deals.proposals.store');

    Route::post('/proposals/{proposal}/send', [DealProposalController::class, 'send'])
        ->name('proposals.send');

    /*
    | Calendar
    */
    Route::get('/calendar', [CalendarEventController::class, 'index'])
        ->name('calendar.index');

    Route::post('/calendar', [CalendarEventController::class, 'store'])
        ->name('calendar.store');

    /*
    | Entities & People
    */
    Route::resource('entities', EntityController::class);
    Route::resource('people', PersonController::class);

    /*
    | Profile
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
