<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use App\Models\Academy;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $courses = Course::all();

        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academy_id' => ['required', 'integer', 'exists:academies,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $academy = Academy::findOrFail($validated['academy_id']);

        if ($academy->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para crear cursos en esta academia.'
            ], 403);
        }

        $course = $academy->courses()->create($validated);

        return response()->json($course, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, Course $course): JsonResponse
    {
        if ($course->academy->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar este curso.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
        ]);

        $startDate = Carbon::parse($validated['start_date'] ?? $course->start_date);
        $endDate = Carbon::parse($validated['end_date'] ?? $course->end_date);

        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'La fecha de finalización no puede ser anterior a la de inicio.'
            ]);
        }

        $course->update($validated);

        return response()->json($course, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(Request $request, Course $course): Response
    {
        if ($course->academy->user_id !== $request->user()->id) {
            abort(403, 'No tienes permiso para eliminar este curso.');
        }

        $course->delete();

        return response()->noContent();
    }

}
