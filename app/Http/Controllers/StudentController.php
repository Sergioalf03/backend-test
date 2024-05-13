<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentLecture;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    private function generateControlNumber($givenName, $surname) {
        $currentYear = Carbon::now()->year;

        $formattedName = substr($givenName, 0, 2);

        return $currentYear . 'ESC' . strtoupper($formattedName) . strtoupper($surname);
    }

    public function saveStudent(Request $request) {
        $student = new Student;

        $studentExists = $student::where('givenName', $request['givenName'])
            ->where('givenName', $request['givenName'])
            ->where('status', 1)
            ->first();

        if (isset($studentExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El nombre que estás enviando ya está registrado');
        }

        $emailExists = $student::where('email', $request['email'])
            ->where('status', 1)
            ->first();

        if (isset($emailExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El correo que estás enviando ya está registrado');
        }

        $studentSaveResult = $student::create([
            'givenName' => $request['givenName'],
            'surname' => $request['surname'],
            'email' => $request['email'],
            'phoneNumber' => $request['phoneNumber'],
            'identificationNumber' => $this->generateControlNumber($request['givenName'], $request['surname']),
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => [
                'id' => $studentSaveResult['id'],
            ],
        ]);
    }

    public function getStudentList(Request $request) {
        $student = new Student;

        $studentResult = $student::where('status', 1)
            ->select(
                'id',
                'givenName',
                'surname',
                'email',
            )
            ->get();

        return response()->json([
            'message' => 'Success',
            'data' => $studentResult,
        ]);
    }

    public function getStudentById(Request $request, $studentId) {
        $student = new Student;

        $studentExists = $student::where('id', $studentId)
            ->where('status', 1)
            ->select(
                'givenName',
                'surname',
                'email',
                'phoneNumber',
                'identificationNumber',
            )
            ->first();

        if (!isset($studentExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El estudiante no existe o está eliminado');
        }

        return response()->json([
            'message' => 'Success',
            'data' => $studentExists,
        ]);
    }

    public function deleteStudent(Request $request, $studentId) {
        $student = new Student;

        $studentExists = $student::where('id', $studentId)
            ->where('status', 1)
            ->select(
               'id'
            )
            ->first();

        if (!isset($studentExists)) {
            return abort(env('ERROR_STATUS_CODE'), 'El estudiante no existe o está eliminado');
        }

        // Validates that the student does not have any lectures assigned
        $studentLecture = new StudentLecture;
        $studentLectureCount = $studentLecture::where('studentId', $studentId)
            ->where('status', 1)
            ->count();

        if ($studentLectureCount > 0) {
            return abort(env('ERROR_STATUS_CODE'), 'El estudiante no se puede eliminar porque tiene materias asignadas');
        }

        $studentResult = $studentExists->fill(['status' => 0])->save();

        return response()->json([
            'message' => 'Success',
        ]);
    }

}
