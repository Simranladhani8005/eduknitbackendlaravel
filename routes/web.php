<?php
use App\Http\Controllers\CourseController;

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// routes/web.php
 use App\Http\Controllers\UsersController;
 

Route::get('/register', function () {
    return view('register');
});
Route::get('/login', function () {
    return view('login');
});
Route::get('/dashboard', [UsersController::class, 'dashboard'])->name('dashboard')->middleware('auth');
Route::resource('courses', CourseController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/enrolled-courses', [CourseController::class, 'getEnrolledCourses']);
    Route::post('/api/enroll/{courseId}', [CourseController::class, 'enroll']);
});


