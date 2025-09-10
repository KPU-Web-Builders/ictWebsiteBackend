<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FaqCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FaqCategory::query();

        // Filter by active status
        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Include FAQ counts
        if ($request->boolean('with_counts')) {
            $query->withCount(['faqs', 'activeFaqs']);
        }

        // Include FAQs
        if ($request->boolean('with_faqs')) {
            if ($request->boolean('active_faqs_only')) {
                $query->with('activeFaqs');
            } else {
                $query->with('faqs');
            }
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
        $query = FaqCategory::query();

        // Include FAQs if requested
        if (request()->boolean('with_faqs')) {
            if (request()->boolean('active_faqs_only')) {
                $query->with('activeFaqs');
            } else {
                $query->with('faqs');
            }
        }

        // Include FAQ counts
        if (request()->boolean('with_counts')) {
            $query->withCount(['faqs', 'activeFaqs']);
        }

        $category = $query->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ category not found'
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
            'name' => 'required|string|max:100|unique:faq_categories,name',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        $category = FaqCategory::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ category created successfully',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $category = FaqCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ category not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:faq_categories,name,' . $id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ category updated successfully',
            'data' => $category->fresh()
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $category = FaqCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ category not found'
            ], 404);
        }

        // Check if category has FAQs
        $faqsCount = $category->faqs()->count();
        if ($faqsCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Cannot delete category. It has {$faqsCount} FAQ(s) associated with it."
            ], 400);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ category deleted successfully'
        ]);
    }

    public function toggleActive($id): JsonResponse
    {
        $category = FaqCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ category not found'
            ], 404);
        }

        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ category status updated successfully',
            'data' => $category->fresh()
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:faq_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['categories'] as $categoryData) {
            FaqCategory::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ categories reordered successfully'
        ]);
    }

    public function getActiveWithFaqs(): JsonResponse
    {
        $categories = FaqCategory::active()
            ->with('activeFaqs')
            ->withCount('activeFaqs')
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}