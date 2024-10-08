<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Contest\ContestController;
use App\Http\Controllers\Contest\ContestPaperController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\FileController;

use App\Http\Controllers\Contest\GenerateFiles;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\CheckContestAffliation;
use App\Http\Middleware\CheckIfUserIsMember;
use App\Http\Middleware\CheckIfUserIsChecker;

use App\Http\Controllers\Home;

Route::get('/', [Home::class, 'showHome'])->middleware('auth')->name('home');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->middleware(RedirectIfAuthenticated::class)
    ->name('register.form');

Route::post('register', [RegisterController::class, 'register'])
    ->name('register');

Route::post('agree', [ExpertController::class, 'agree'])
    ->name('agree');

Route::get('contest/{contest_id}', [ContestController::class, 'showContest'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->name('contests.show');

Route::get('contest/{contest_id}/rating', [ContestController::class, 'showRating'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->name('ratings.show');


Route::get('contest/{contest_id}/appeal/{appeal_id}', [ContestController::class, 'showAppeal'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('appeal_id', '[0-9]+')
    ->name('appeals.show');

Route::get('contest/{contest_id}/ppi_files', [ContestController::class, 'getPPIFilesArchive'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+');

Route::get('contest/{contest_id}/member', [ContestController::class, 'showContestMemberCheck'])
    ->middleware('auth')
    ->middleware(CheckIfUserIsMember::class)
    ->where('contest_id', '[0-9]+')
    ->name('contestMemberCheck.show');

Route::get('contest/{contest_id}/check', [ExpertController::class, 'showContestCheck'])
    ->middleware('auth')
    ->middleware(CheckIfUserIsChecker::class)
    ->where('contest_id', '[0-9]+')
    ->name('contestCheck.show');

Route::get('contest/{contest_id}/check/{contest_member_id}', [ExpertController::class, 'showUserContestCheck'])
    ->middleware('auth')
    ->middleware(CheckIfUserIsChecker::class)
    ->where('contest_id', '[0-9]+')
    ->where('contest_member_id', '[0-9]+')
    ->name('contestCheck.showUser');

Route::get('contest/{contest_id}/place/{place_id}',
    [ContestController::class, 'showPlace'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class)
    ->where('contest_id', '[0-9]+')
    ->where('place_id', '[0-9]+')
    ->name('places.show');

Route::get('/scan/{filename}',
    [FileController::class, 'getScan'])
    ->middleware('auth');

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


Route::get('contest/{contest_id}/rating/download',
    [GenerateFiles::class, 'generateXLSXRating'])
    ->middleware('auth')
    ->middleware(CheckContestAffliation::class);


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
Route::post('stop_appeals', [ContestController::class, 'stopAppeals'])->middleware('auth');
Route::post('start_appeals', [ContestController::class, 'startAppeals'])->middleware('auth');
Route::post('start_checking', [ContestController::class, 'startChecking'])->middleware('auth');
Route::post('start_generation', [ContestPaperController::class, 'startGeneration'])->middleware('auth');
Route::post('send_files', [ContestController::class, 'sendFiles'])->middleware('auth');
Route::post('save_grades', [ExpertController::class, 'saveGrades'])->middleware('auth')->name('saveGrades');
Route::post('send_appeal', [ContestController::class, 'sendAppeal'])->middleware('auth')->name('sendAppeal');
Route::post('refuse_to_work', [ExpertController::class, 'refuseToWork'])->middleware('auth')->name('refuseToWork');
Route::post('request_new_work', [ExpertController::class, 'requestNewWork'])->middleware('auth')->name('requestNewWork');

Route::post('publish_results', [ContestController::class, 'publishResults'])->middleware('auth');




Route::get('get_cities', [ContestController::class, 'getCities'])->middleware('auth');
Route::get('get_schools', [ContestController::class, 'getSchools'])->middleware('auth');
Route::get('prototype_data', [ContestController::class, 'getPrototypeData'])->middleware('auth');
Route::get('get_appeals', [ContestController::class, 'getAppeals'])->middleware('auth');
Route::get('get_results', [ContestController::class, 'getResults'])->middleware('auth');

Route::get('contest/{contest_id}/notification', [ContestController::class, 'showNotification'])
    ->middleware('auth')
    ->middleware(CheckIfUserIsMember::class)
    ->where('contest_id', '[0-9]+')
    ->name('notification.show');

Route::get('contest/{contest_id}/option', [ContestController::class, 'showOption'])
    ->middleware('auth')
    ->where('contest_id', '[0-9]+')
    ->name('option.show');
