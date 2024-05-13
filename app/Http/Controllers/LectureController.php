<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\StudentLecture;
use Illuminate\Http\Request;

class LectureController extends Controller
{

    public function saveLecture(Request $request) {
        $lecture = new Lecture;

        // Valida que no exista el nombre
        $lectureExists = $lecture::where('name', $request['name'])
            ->where('status', 1)
            ->first();

        if (isset($lectureExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El nombre de la materia ya está registrado');
        }

        // Valida que no exista el maestro
        $teacherExists = $lecture::where('teacher', $request['teacher'])
            ->where('status', 1)
            ->first();

        if (isset($teacherExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El maestro ya está asignado a otra materia');
        }

        // Valida que no exista el horario
        $scheduleExists = $lecture::where('schedule', $request['schedule'])
            ->where('status', 1)
            ->first();

        if (isset($scheduleExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El horario ya se encuentra ocupado');
        }

        $lectureSaveResult = $lecture::create([
            'name' => $request['name'],
            'teacher' => $request['teacher'],
            'schedule' => $request['schedule'],
            'status' => $request['status'],
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => [
                'id' => $lectureSaveResult['id'],
            ],
        ]);
    }

    public function getLectureById(Request $request, $lectureId) {
        $lecture = new Lecture;

        $lectureExists = $lecture::where('id', $lectureId)
            ->where('status', 1)
            ->select(
                'name',
                'teacher',
                'schedule',
            )
            ->first();

        // Valida que exista la materia
        if (!isset($lectureExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'La materia no existe o está eliminada');
        }

        return response()->json([
            'message' => 'Success',
            'data' => $lectureExists,
        ]);
    }

    public function deleteLecture(Request $request, $lectureId) {
        $lecture = new Lecture;

        $lectureExists = $lecture::where('id', $lectureId)
            ->where('status', 1)
            ->select(
               'id'
            )
            ->first();

        // Valida que exista la materia
        if (!isset($lectureExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'La materia no existe o está eliminada');
        }

        // Validación de no tener materias
        $studentLecture = new StudentLecture;
        $studentLectureCount = $studentLecture::where('lectureId', $lectureId)
            ->where('status', 1)
            ->count();

        if ($studentLectureCount > 0) {
            return abort(env('ERROR_STATUS_CODE'), 'La materia no se puede eliminar porque tiene alumnos asignadas');
        }

        $lectureResult = $lectureExists->fill(['status' => 0])->save();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    public function assignStudentToLecture(Request $request) {
        $studentLecture = new StudentLecture;
        $studentLectureExists = $studentLecture::where('lectureId', $request['lectureId'])
            ->where('studentId', $request['studentId'])
            ->where('status', 1)
            ->first();

        // Valida que exista la asignación
        if (isset($studentLectureExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'La asignación que deseas realizar ya existe');
        }

        $studentLectureResult = $studentLecture::create([
            'lectureId' => $request['studentId'],
            'studentId' => $request['lectureId'],
            'status' => $request['status'],
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => [
                'id' => $studentLectureResult['id'],
            ],
        ]);
    }

}
