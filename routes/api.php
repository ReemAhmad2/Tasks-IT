<?php

use App\Http\Controllers\Api\Auth\StudentController;
use App\Http\Controllers\Api\Auth\TeacherController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Student\StudentController as StudentStudentController;
use App\Http\Controllers\Api\Task\CommentController;
use App\Http\Controllers\Api\Task\SubmissionController;
use App\Http\Controllers\Api\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function PHPSTORM_META\type;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login',[UserController::class,'login'])->middleware('guest:sanctum');
Route::post('/register/student',[StudentController::class,'register'])
        ->middleware('guest:sanctum');
Route::post('/register/teacher',[TeacherController::class,'register'])
        ->middleware(['auth:sanctum','type:admin']);
Route::post('/logout',[UserController::class,'logout'])->middleware('auth:sanctum');


Route::post('/add/task',[TaskController::class,'store'])
        ->middleware(['auth:sanctum','type:teacher']);
Route::post('delete/task/{uuid}',[TaskController::class,'deleteTask'])
        ->middleware(['auth:sanctum','type:teacher,admin']);
Route::post('show/task',[TaskController::class,'show'])
        ->middleware(['auth:sanctum','access_task']);
Route::get('all/tasks',[TaskController::class,'index'])
        ->middleware(['auth:sanctum','type:admin']);
Route::post('all/task/student',[TaskController::class,'allTasksForStudent'])
        ->middleware(['auth:sanctum','type:student']);


Route::post('add/comment',[CommentController::class,'store'])
        ->middleware(['auth:sanctum','type:teacher,student','access_task']);
Route::post('delete/comment',[CommentController::class,'delete'])
        ->middleware(['auth:sanctum','type:teacher,student']);
Route::post('all/comment',[CommentController::class,'index'])
        ->middleware(['auth:sanctum','type:teacher,student','access_task']);


Route::post('file/task/upload',[SubmissionController::class,'upload'])
        ->middleware(['auth:sanctum','type:student','access_submit']);
Route::post('add/partners',[SubmissionController::class,'addPartners'])
        ->middleware(['auth:sanctum','type:student']);
Route::post('all/files/task',[SubmissionController::class,'submissionsForTask'])
        ->middleware(['auth:sanctum','type:teacher','access_task']);


Route::post('students-it-import',[StudentStudentController::class,'addStudentsIT'])
        ->middleware(['auth:sanctum','type:admin']);
Route::post('search/tasks',[TaskController::class,'searchTask'])
        ->middleware(['auth:sanctum','type:admin']);

