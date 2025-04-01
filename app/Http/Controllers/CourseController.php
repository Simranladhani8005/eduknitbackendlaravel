<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index() {
        $courses = Course::all()->map(function ($course) {
            $course->file_paths = json_decode($course->file_paths, true) ?? [];
            return $course;
        });
        return response()->json($courses, 200);
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'duration' => 'nullable|string',
            'files.*' => 'nullable|mimes:jpeg,png,jpg,mp4,avi,mov,txt,pdf|max:20480',
        ]);

        $data = $request->only(['title', 'description', 'price', 'user_id', 'duration']);
        $data['duration'] = $data['duration'] ?? '4 weeks';
        $uploadedFiles = [];

        // Handle multiple file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads', 'public');
                $uploadedFiles[] = 'storage/' . $path; // Ensure correct URL for viewing
            }
        }
        $data['file_paths'] = json_encode($uploadedFiles);

        $course = Course::create($data);
        return response()->json($course, 201);
    }

    public function show($id) {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }
        $course->file_paths = json_decode($course->file_paths, true) ?? [];
        return response()->json($course, 200);
    }

    public function update(Request $request, $id) {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'user_id' => 'nullable|exists:users,id',
            'duration' => 'nullable|string',
            'files.*' => 'nullable|mimes:jpeg,png,jpg,mp4,avi,mov,txt,pdf|max:20480',
        ]);

        $data = $request->except(['files']);
        $data['duration'] = $request->input('duration', $course->duration);
        $uploadedFiles = json_decode($course->file_paths, true) ?? [];

        // Handle file updates
        if ($request->hasFile('files')) {
            // Delete old files
            foreach ($uploadedFiles as $path) {
                $actualPath = str_replace('storage/', '', $path);
                if (Storage::exists('public/' . $actualPath)) {
                    Storage::delete('public/' . $actualPath);
                }
            }

            $uploadedFiles = [];
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads', 'public');
                $uploadedFiles[] = 'storage/' . $path;
            }
        }
        $data['file_paths'] = json_encode($uploadedFiles);

        $course->update($data);
        return response()->json($course, 200);
    }

    public function destroy($id) {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        // Delete associated files
        $uploadedFiles = json_decode($course->file_paths, true) ?? [];
        foreach ($uploadedFiles as $path) {
            $actualPath = str_replace('storage/', '', $path);
            if (Storage::exists('public/' . $actualPath)) {
                Storage::delete('public/' . $actualPath);
            }
        }

        $course->delete();
        return response()->json(['message' => 'Course deleted successfully'], 200);
    }


    public function enroll(Request $request, $courseId) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please login to enroll.'], 401);
        }
    
        $user = Auth::user();
    
        // Check if the user is already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)->where('course_id', $courseId)->first();
        if ($existingEnrollment) {
            return response()->json(['error' => 'You are already enrolled in this course.'], 400);
        }
    
        // Create a new enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
        ]);
    
        return response()->json(['message' => 'Enrollment successful!']);
    }

    public function getEnrolledCourses() {
        $user = Auth::user();
        $enrolledCourses = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->get();
    
        return response()->json($enrolledCourses);
    }

   
}
