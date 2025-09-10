<?php

namespace App\Http\Controllers;

use App\Models\HostingPlan;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class HostingPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = HostingPlan::with('category');

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by highlighted
        if ($request->has('highlighted')) {
            $query->where('is_highlighted', $request->boolean('highlighted'));
        }

        // Filter by popular
        if ($request->has('popular')) {
            $query->where('is_popular', $request->boolean('popular'));
        }

        // Apply ordering
        $query->ordered();

        $plans = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $plans
        ]);
    }

    public function show($id): JsonResponse
    {
        $plan = HostingPlan::with('category')->find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $plan
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:hosting_plans,slug',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'monthly_renewal_price' => 'required|numeric|min:0',
            'yearly_renewal_price' => 'required|numeric|min:0',
            'is_highlighted' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug uniqueness
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (HostingPlan::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $plan = HostingPlan::create($validated);
        $plan->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'Hosting plan created successfully',
            'data' => $plan
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $plan = HostingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'slug' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('hosting_plans')->ignore($id)],
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|exists:service_categories,id',
            'monthly_price' => 'sometimes|required|numeric|min:0',
            'yearly_price' => 'sometimes|required|numeric|min:0',
            'monthly_renewal_price' => 'sometimes|required|numeric|min:0',
            'yearly_renewal_price' => 'sometimes|required|numeric|min:0',
            'is_highlighted' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Auto-generate slug if name changed but slug not provided
        if (isset($validated['name']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure slug uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (HostingPlan::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $plan->update($validated);
        $plan->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'Hosting plan updated successfully',
            'data' => $plan->fresh(['category'])
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $plan = HostingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Hosting plan deleted successfully'
        ]);
    }

    public function toggleActive($id): JsonResponse
    {
        $plan = HostingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        $plan->update(['is_active' => !$plan->is_active]);
        $plan->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'Hosting plan status updated successfully',
            'data' => $plan->fresh(['category'])
        ]);
    }

    public function toggleHighlighted($id): JsonResponse
    {
        $plan = HostingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        $plan->update(['is_highlighted' => !$plan->is_highlighted]);
        $plan->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'Hosting plan highlight status updated successfully',
            'data' => $plan->fresh(['category'])
        ]);
    }

    public function togglePopular($id): JsonResponse
    {
        $plan = HostingPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        $plan->update(['is_popular' => !$plan->is_popular]);
        $plan->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'Hosting plan popular status updated successfully',
            'data' => $plan->fresh(['category'])
        ]);
    }
}