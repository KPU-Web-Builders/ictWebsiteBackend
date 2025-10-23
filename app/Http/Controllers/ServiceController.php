<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::with('category');

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Apply ordering
        $query->ordered();

        $services = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

    public function show($id): JsonResponse
    {
        $service = Service::with('category')->find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $service
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:services,slug',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:service_categories,id',
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
        while (Service::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $service = Service::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Service created successfully',
            'data' => $service->load('category')
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:200',
            'slug' => ['sometimes', 'nullable', 'string', 'max:200', Rule::unique('services')->ignore($id)],
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:service_categories,id',
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
            while (Service::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $service->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Service updated successfully',
            'data' => $service->fresh()->load('category')
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        try {
            $service->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'FOREIGN KEY constraint failed')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this service because it has associated records. Please delete or reassign related items first.'
                ], 409);
            }

            // Re-throw other database exceptions
            throw $e;
        }
    }

    public function toggleActive($id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $service->update(['is_active' => !$service->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Service status updated successfully',
            'data' => $service->fresh()->load('category')
        ]);
    }

    public function getByCategory($categoryId): JsonResponse
    {
        $services = Service::where('category_id', $categoryId)
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }
}
