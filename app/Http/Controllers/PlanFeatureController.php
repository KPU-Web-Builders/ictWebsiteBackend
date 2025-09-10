<?php

namespace App\Http\Controllers;

use App\Models\PlanFeature;
use App\Models\HostingPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlanFeatureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PlanFeature::with('plan.category');

        // Filter by plan
        if ($request->has('plan_id')) {
            $query->byPlan($request->plan_id);
        }

        // Filter by included status
        if ($request->has('included')) {
            if ($request->boolean('included')) {
                $query->included();
            } else {
                $query->notIncluded();
            }
        }

        // Apply ordering
        $query->ordered();

        $features = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $features
        ]);
    }

    public function show($id): JsonResponse
    {
        $feature = PlanFeature::with('plan.category')->find($id);

        if (!$feature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan feature not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $feature
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:hosting_plans,id',
            'feature_name' => 'required|string|max:200',
            'is_included' => 'required|boolean',
            'feature_value' => 'nullable|string|max:100',
            'tooltip' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $feature = PlanFeature::create($validated);
        $feature->load('plan.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Plan feature created successfully',
            'data' => $feature
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $feature = PlanFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan feature not found'
            ], 404);
        }

        $validated = $request->validate([
            'plan_id' => 'sometimes|required|exists:hosting_plans,id',
            'feature_name' => 'sometimes|required|string|max:200',
            'is_included' => 'sometimes|required|boolean',
            'feature_value' => 'nullable|string|max:100',
            'tooltip' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $feature->update($validated);
        $feature->load('plan.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Plan feature updated successfully',
            'data' => $feature->fresh(['plan.category'])
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $feature = PlanFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan feature not found'
            ], 404);
        }

        $feature->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Plan feature deleted successfully'
        ]);
    }

    public function toggleIncluded($id): JsonResponse
    {
        $feature = PlanFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan feature not found'
            ], 404);
        }

        $feature->update(['is_included' => !$feature->is_included]);
        $feature->load('plan.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Plan feature inclusion status updated successfully',
            'data' => $feature->fresh(['plan.category'])
        ]);
    }

    public function getByPlan($planId): JsonResponse
    {
        $plan = HostingPlan::find($planId);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hosting plan not found'
            ], 404);
        }

        $features = PlanFeature::byPlan($planId)->ordered()->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'plan' => $plan,
                'features' => $features
            ]
        ]);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:hosting_plans,id',
            'features' => 'required|array|min:1',
            'features.*.feature_name' => 'required|string|max:200',
            'features.*.is_included' => 'required|boolean',
            'features.*.feature_value' => 'nullable|string|max:100',
            'features.*.tooltip' => 'nullable|string',
            'features.*.sort_order' => 'nullable|integer|min:0'
        ]);

        $createdFeatures = [];
        foreach ($validated['features'] as $index => $featureData) {
            $featureData['plan_id'] = $validated['plan_id'];
            $featureData['sort_order'] = $featureData['sort_order'] ?? $index;
            
            $feature = PlanFeature::create($featureData);
            $createdFeatures[] = $feature;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Plan features created successfully',
            'data' => $createdFeatures
        ], 201);
    }
}