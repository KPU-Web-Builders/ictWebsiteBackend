<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Faq::with('category');

        // Filter by active status
        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Search in question and answer
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply ordering
        $query->ordered();

        // Pagination
        $faqs = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $faqs
        ]);
    }

    public function show($id): JsonResponse
    {
        $faq = Faq::with('category')->find($id);

        if (!$faq) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $faq
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:faq_categories,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        $faq = Faq::create($validated);
        $faq->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ created successfully',
            'data' => $faq
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ not found'
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:faq_categories,id',
            'question' => 'sometimes|required|string',
            'answer' => 'sometimes|required|string',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        $faq->update($validated);
        $faq->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ updated successfully',
            'data' => $faq->fresh(['category'])
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ not found'
            ], 404);
        }

        $faq->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ deleted successfully'
        ]);
    }

    public function toggleActive($id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ not found'
            ], 404);
        }

        $faq->update(['is_active' => !$faq->is_active]);
        $faq->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ status updated successfully',
            'data' => $faq->fresh(['category'])
        ]);
    }

    public function toggleFeatured($id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ not found'
            ], 404);
        }

        $faq->update(['is_featured' => !$faq->is_featured]);
        $faq->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ featured status updated successfully',
            'data' => $faq->fresh(['category'])
        ]);
    }

    public function getByCategory($categoryId): JsonResponse
    {
        $category = FaqCategory::find($categoryId);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'FAQ category not found'
            ], 404);
        }

        $faqs = Faq::byCategory($categoryId)->active()->ordered()->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $category,
                'faqs' => $faqs,
                'count' => $faqs->count()
            ]
        ]);
    }

    public function getFeatured(): JsonResponse
    {
        $faqs = Faq::with('category')
            ->active()
            ->featured()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $faqs
        ]);
    }

    public function getPublic(): JsonResponse
    {
        $faqs = Faq::with('category')
            ->active()
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->ordered()
            ->get()
            ->groupBy('category.name');

        return response()->json([
            'status' => 'success',
            'data' => $faqs
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:3'
        ]);

        $query = $request->q;

        $faqs = Faq::with('category')
            ->active()
            ->whereHas('category', function ($q) {
                $q->where('is_active', true);
            })
            ->search($query)
            ->ordered()
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'query' => $query,
                'results' => $faqs,
                'count' => $faqs->count()
            ]
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'faqs' => 'required|array',
            'faqs.*.id' => 'required|exists:faqs,id',
            'faqs.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['faqs'] as $faqData) {
            Faq::where('id', $faqData['id'])
                ->update(['sort_order' => $faqData['sort_order']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FAQs reordered successfully'
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:faqs,id',
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'category_id' => 'sometimes|nullable|exists:faq_categories,id'
        ]);

        $faqs = Faq::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'activate':
                $faqs->update(['is_active' => true]);
                $message = 'FAQs activated successfully';
                break;
            case 'deactivate':
                $faqs->update(['is_active' => false]);
                $message = 'FAQs deactivated successfully';
                break;
            case 'feature':
                $faqs->update(['is_featured' => true]);
                $message = 'FAQs featured successfully';
                break;
            case 'unfeature':
                $faqs->update(['is_featured' => false]);
                $message = 'FAQs unfeatured successfully';
                break;
            case 'delete':
                $faqs->delete();
                $message = 'FAQs deleted successfully';
                break;
        }

        if (isset($validated['category_id'])) {
            $faqs->update(['category_id' => $validated['category_id']]);
            $message = 'FAQs updated successfully';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }
}