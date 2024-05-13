<?php

use App\Http\Controllers\LectureController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// api/

Route::post('/student/save', [StudentController::class, 'saveStudent']);
Route::get('/student/list', [StudentController::class, 'getStudentList']);
Route::get('/student/by-id/{studentId}', [StudentController::class, 'getStudentById']);
Route::delete('/student/by-id/{studentId}', [StudentController::class, 'deleteStudent']);

Route::post('/lecture/save', [LectureController::class, 'saveLecture']);
Route::get('/lecture/by-id/{lectureId}', [LectureController::class, 'getLectureById']);
Route::delete('/lecture/by-id/{lectureId}', [LectureController::class, 'deleteLecture']);
Route::post('lecture/assign-student', [LectureController::class, 'assignStudentToLecture']);
