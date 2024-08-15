<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Contest\ContestController;

use App\Http\Middleware\RedirectIfAuthenticated;

use App\Http\Controllers\Home;

Route::get('/', [Home::class, 'showHome'])->middleware('auth')->name('home');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->middleware(RedirectIfAuthenticated::class)
    ->name('register.form');

Route::post('register', [RegisterController::class, 'register'])
    ->name('register');

Route::get('contest/{contest_id}', [ContestController::class, 'showContest'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->name('contests.show');

Route::get('contest/{contest_id}/check', [ContestController::class, 'showContestCheck'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->name('contestCheck.show');

Route::get('contest/{contest_id}/place/{place_id}',
    [ContestController::class, 'showPlace'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+')
    ->name('places.show');

Route::get('contest/{contest_id}/level/{level_id}',
    [ContestController::class, 'showLevel'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->where('level_id', '[0-9]+')
    ->name('level.show');

Route::get('contest/{contest_id}/level/{level_id}/task/{task_id}',
    [ContestController::class, 'showTask'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->where('level_id', '[0-9]+')
    ->where('task_id', '[0-9]+')
    ->name('task.show');

Route::get('login', [LoginController::class, 'showLoginForm'])
    ->middleware(RedirectIfAuthenticated::class)
    ->name('login');

Route::post('login', [LoginController::class, 'login']);

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::post('new_particip', [ContestController::class, 'newParticip']);
Route::post('new_contest', [ContestController::class, 'newContest']);
Route::post('get_contest', [ContestController::class, 'getContest']);
Route::post('new_place', [ContestController::class, 'newPlace']);
Route::post('new_level', [ContestController::class, 'newLevel']);
Route::post('new_task', [ContestController::class, 'newTask']);
Route::post('new_expert', [ContestController::class, 'newExpert']);
Route::post('new_auditorium', [ContestController::class, 'newAuditorium']);
Route::post('set_pattern', [ContestController::class, 'setPattern']);

Route::get('contest/{contest_id}/notification', [ContestController::class, 'showNotification'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->name('notification.show');
