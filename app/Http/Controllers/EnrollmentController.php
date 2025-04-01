<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enrollments(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        Enrollment::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
        ]);

        return response()->json(['message' => 'Enrolled successfully!'], 201);
    }
}
