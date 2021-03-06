<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create somereturn $next($request);thing great!
|
*/
Route::get('/home', function () {
  if (Auth::check()) {
    if (Auth::user()->isTeacher()) {
      return redirect('/teacher');
    } else {
      return redirect('/student');
    }
  }
  return redirect('/login');
});

Route::get('/', function () {
  return redirect('home');
})->name('home');

Auth::routes();

Route::group(['prefix' => 'student', 'middleware' => ['ensureStudent']], function () {
  Route::get('/', [\App\Http\Controllers\TestController::class, 'studentTests']);
  Route::get('/test/{id}', [\App\Http\Controllers\TestController::class, 'attemptTest'])->middleware('canFillTest');
  Route::post('/test/saveAttempt', [\App\Http\Controllers\TestController::class, 'saveAttempt'])->name('saveAttempt');
});

Route::group(['prefix' => 'teacher', 'middleware' => ['ensureTeacher']], function () {
  Route::get('/', function () {
    return view('teacher.teacherPanel', ['teacher' => Auth::user()]);
  });

  Route::get('/student/results', [\App\Http\Controllers\TestController::class, 'studentResults']);
  //scieżki dotyczące uczniów
  Route::get('/students', [\App\Http\Controllers\UserController::class, 'studentList']);
  Route::get('/addStudent', function () {
    return view('teacher.addStudent');
  });
  Route::post('/addStudent', [\App\Http\Controllers\UserController::class, 'addStudent'])->name('addStudent');
  Route::post('/student/d/{id}', [\App\Http\Controllers\UserController::class, 'delStudent'])->name('delStudent');
  Route::post('/student/{id}', [\App\Http\Controllers\UserController::class, 'saveEditStudent'])->name('saveEditStudent');
  Route::get('/student/{id}', [\App\Http\Controllers\UserController::class, 'editStudent']);



//  ścieżki dotyczące pytań (pojedyńczych)
  Route::get('/exercises', [\App\Http\Controllers\ExerciseController::class, 'exercisesList']);
  Route::get('/addExercise', function () {
    return view('exercises.addExercise');
  });
  Route::post('/addExercise', [\App\Http\Controllers\ExerciseController::class, 'addExercise'])->name('addExercise');
  Route::get('/exercise/{id}', [\App\Http\Controllers\ExerciseController::class, 'editExercise']);
  Route::post('/exercise/{id}', [\App\Http\Controllers\ExerciseController::class, 'saveEditExercise'])->name('saveEditExercise');
  Route::post('/exercise/d/{id}', [\App\Http\Controllers\ExerciseController::class, 'delExercise'])->name('delExercise');

  // scieżki dotyczące testów
  Route::get('/tests', [\App\Http\Controllers\TestController::class, 'testList']);
  Route::post('/test/d/{id}', [\App\Http\Controllers\TestController::class, 'delTest'])->name('delTest');
  Route::get('/addTest', function () {
    $exercises = \App\Models\Exercise::all();
    return view('tests.addTest', ['exercises' => $exercises]);
  });
  Route::post('/addTest', [\App\Http\Controllers\TestController::class, 'addTest'])->name('addTest');

  //klasy
  Route::get('/classes', [\App\Http\Controllers\ClassController::class, 'classList']);
  Route::get('/addClass', function () {
    $students = \App\Models\User::all()->where('isTeacher', '=', '0');
    return view('classes.addClass', ['students' => $students]);
  });
  Route::post('/addClass', [\App\Http\Controllers\ClassController::class, 'addClass'])->name('addClass');
  Route::post('/delClass/{id}', [\App\Http\Controllers\ClassController::class, 'delClass'])->name('delClass');
  Route::post('/addTestToClass', [\App\Http\Controllers\ClassController::class, 'addTestToClass'])->name('addTestToClass');
  Route::post('/addStudentToClass', [\App\Http\Controllers\ClassController::class, 'addStudentToClass'])->name('addStudentToClass');
});
