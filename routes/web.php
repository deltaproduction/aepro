<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Contest\ContestController;
use App\Http\Controllers\Contest\ContestPaperController;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\CheckContestAffliation;

use App\Http\Controllers\Home;

Route::get('/', [Home::class, 'showHome'])->middleware('auth')->name('home');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->middleware(RedirectIfAuthenticated::class)
    ->name('register.form');

Route::post('register', [RegisterController::class, 'register'])
    ->name('register');

Route::get('contest/{contest_id}', [ContestController::class, 'showContest'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->name('contests.show');

Route::get('contest/{contest_id}/ppi_files', [ContestController::class, 'getPPIFilesArchive'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+');

Route::get('contest/{contest_id}/check', [ContestController::class, 'showContestCheck'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->name('contestCheck.show');

Route::get('contest/{contest_id}/place/{place_id}',
    [ContestController::class, 'showPlace'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+')
    ->name('places.show');

Route::get('contest/{contest_id}/place/{place_id}/protocols',
    [ContestController::class, 'getProtocolsArchive'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+');

Route::get('contest/{contest_id}/place/{place_id}/papers',
    [ContestController::class, 'getPapersArchive'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+');

Route::get('contest/{contest_id}/place/{place_id}/ppi_file',
    [ContestController::class, 'getPPIFile'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+');

Route::get('contest/{contest_id}/place/{place_id}/auditorium/{auditorium_id}',
    [ContestController::class, 'showAuditorium'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+')
    ->where('auditorium_id', '[0-9]+')
    ->name('auditoriums.show');

Route::get('contest/{contest_id}/place/{place_id}/auditorium/{auditorium_id}/protocol',
    [ContestController::class, 'getProtocol'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+')
    ->where('auditorium_id', '[0-9]+');

Route::get('contest/{contest_id}/place/{place_id}/auditorium/{auditorium_id}/papers',
    [ContestController::class, 'getPapers'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+')
    ->where('auditorium_id', '[0-9]+');

Route::get('contest/{contest_id}/level/{level_id}',
    [ContestController::class, 'showLevel'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('level_id', '[0-9]+')
    ->name('level.show');

Route::get('contest/{contest_id}/level/{level_id}/task/{task_id}',
    [ContestController::class, 'showTask'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('level_id', '[0-9]+')
    ->where('task_id', '[0-9]+')
    ->name('task.show');

Route::get('login', [LoginController::class, 'showLoginForm'])
    ->middleware(RedirectIfAuthenticated::class)
    ->name('login');

Route::post('login', [LoginController::class, 'login']);

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::post('new_particip', [ContestController::class, 'newParticip'])->middleware('auth');
Route::post('new_contest', [ContestController::class, 'newContest'])->middleware('auth');
Route::post('get_contest', [ContestController::class, 'getContest'])->middleware('auth');
Route::post('new_place', [ContestController::class, 'newPlace'])->middleware('auth');
Route::post('new_level', [ContestController::class, 'newLevel'])->middleware('auth');
Route::post('new_task', [ContestController::class, 'newTask'])->middleware('auth');
Route::post('new_expert', [ContestController::class, 'newExpert'])->middleware('auth');
Route::post('new_auditorium', [ContestController::class, 'newAuditorium'])->middleware('auth');
Route::post('new_prototype', [ContestController::class, 'newPrototype'])->middleware('auth');
Route::post('set_pattern', [ContestController::class, 'setPattern'])->middleware('auth');
Route::post('set_task_text', [ContestController::class, 'setTaskText'])->middleware('auth');
Route::post('set_tp_data', [ContestController::class, 'setTaskPrototypeData'])->middleware('auth');
Route::post('start_apply', [ContestController::class, 'startApply'])->middleware('auth');
Route::post('stop_apply', [ContestController::class, 'stopApply'])->middleware('auth');
Route::post('end_apply', [ContestController::class, 'endApply'])->middleware('auth');
Route::post('end_tour', [ContestController::class, 'endTour'])->middleware('auth');
Route::post('start_checking', [ContestController::class, 'startChecking'])->middleware('auth');
Route::post('start_generation', [ContestPaperController::class, 'startGeneration'])->middleware('auth');
Route::post('send_files', [ContestController::class, 'sendFiles'])->middleware('auth');

Route::get('get_cities', [ContestController::class, 'getCities'])->middleware('auth');
Route::get('get_schools', [ContestController::class, 'getSchools'])->middleware('auth');
Route::get('prototype_data', [ContestController::class, 'getPrototypeData'])->middleware('auth');


Route::get('contest/{contest_id}/notification', [ContestController::class, 'showNotification'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->name('notification.show');
