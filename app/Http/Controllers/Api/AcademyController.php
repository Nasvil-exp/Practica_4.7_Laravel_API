<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Academy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AcademyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $academies = Academy::all();

        return response()->json($academies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'address' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:20'],
        'email' => ['required', 'email', 'max:255'],
        ]);

        $academy = $request->user()->academies()->create($validated);

        return response()->json($academy, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Academy $academy): JsonResponse
    {
        return response()->json($academy);
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, Academy $academy): JsonResponse
    {
        if ($academy->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta academia.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
        ]);

        $academy->update($validated);

        return response()->json($academy, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(Request $request, Academy $academy): Response
    {
        if ($academy->user_id !== $request->user()->id) {
            abort(403, 'No tienes permiso para eliminar esta academia.');
        }

        $academy->delete();

        return response()->noContent();
    }
}
