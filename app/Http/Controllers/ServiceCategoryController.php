<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceCategory::query();

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Apply ordering
        $query->ordered();

        $categories = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function show($id): JsonResponse
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service category not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:service_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug uniqueness
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (ServiceCategory::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category = ServiceCategory::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Service category created successfully',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service category not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'slug' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('service_categories')->ignore($id)],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        // Auto-generate slug if name changed but slug not provided
        if (isset($validated['name']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure slug uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (ServiceCategory::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Service category updated successfully',
            'data' => $category->fresh()
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service category not found'
            ], 404);
        }

        try {
            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Service category deleted successfully'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'FOREIGN KEY constraint failed')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this category because it has associated records. Please delete or reassign related items first.'
                ], 409);
            }

            // Re-throw other database exceptions
            throw $e;
        }
    }

    public function toggleActive($id): JsonResponse
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service category not found'
            ], 404);
        }

        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Service category status updated successfully',
            'data' => $category->fresh()
        ]);
    }
}