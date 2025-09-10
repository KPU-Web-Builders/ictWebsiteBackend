<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TestimonialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Testimonial::with('service');

        // Filter by approval status
        if ($request->has('approved')) {
            if ($request->boolean('approved')) {
                $query->approved();
            } else {
                $query->where('is_approved', false);
            }
        }

        // Filter by featured status
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Filter by service
        if ($request->has('service_id')) {
            $query->byService($request->service_id);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->byRating($request->rating);
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->minRating($request->min_rating);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply ordering
        $query->ordered();

        // Pagination
        $testimonials = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $testimonials
        ]);
    }

    public function show($id): JsonResponse
    {
        $testimonial = Testimonial::with('service')->find($id);

        if (!$testimonial) {
            return response()->json([
                'status' => 'error',
                'message' => 'Testimonial not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $testimonial
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:100',
            'company' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'testimonial' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'service_id' => 'nullable|exists:services,id',
            'is_featured' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Handle photo upload
        $photoData = $this->handlePhotoUpload($request);
        $validated = array_merge($validated, $photoData);

        $testimonial = Testimonial::create($validated);
        $testimonial->load('service');

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonial created successfully',
            'data' => $testimonial
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'status' => 'error',
                'message' => 'Testimonial not found'
            ], 404);
        }

        $validated = $request->validate([
            'client_name' => 'sometimes|required|string|max:100',
            'company' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'testimonial' => 'sometimes|required|string',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'service_id' => 'nullable|exists:services,id',
            'is_featured' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Handle photo upload
        $photoData = $this->handlePhotoUpload($request, $testimonial);
        $validated = array_merge($validated, $photoData);

        $testimonial->update($validated);
        $testimonial->load('service');

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonial updated successfully',
            'data' => $testimonial->fresh(['service'])
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'status' => 'error',
                'message' => 'Testimonial not found'
            ], 404);
        }

        // Delete associated photo
        $this->deleteTestimonialPhoto($testimonial);

        $testimonial->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonial deleted successfully'
        ]);
    }

    public function toggleApproved($id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'status' => 'error',
                'message' => 'Testimonial not found'
            ], 404);
        }

        $testimonial->update(['is_approved' => !$testimonial->is_approved]);
        $testimonial->load('service');

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonial approval status updated successfully',
            'data' => $testimonial->fresh(['service'])
        ]);
    }

    public function toggleFeatured($id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'status' => 'error',
                'message' => 'Testimonial not found'
            ], 404);
        }

        $testimonial->update(['is_featured' => !$testimonial->is_featured]);
        $testimonial->load('service');

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonial featured status updated successfully',
            'data' => $testimonial->fresh(['service'])
        ]);
    }

    public function getPublic(): JsonResponse
    {
        $testimonials = Testimonial::with('service')
            ->approved()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $testimonials
        ]);
    }

    public function getFeatured(): JsonResponse
    {
        $testimonials = Testimonial::with('service')
            ->approved()
            ->featured()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $testimonials
        ]);
    }

    public function getByService($serviceId): JsonResponse
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $testimonials = Testimonial::byService($serviceId)
            ->approved()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'service' => $service,
                'testimonials' => $testimonials,
                'count' => $testimonials->count(),
                'average_rating' => $testimonials->avg('rating')
            ]
        ]);
    }

    public function getStats(): JsonResponse
    {
        $stats = [
            'total' => Testimonial::count(),
            'approved' => Testimonial::approved()->count(),
            'pending' => Testimonial::where('is_approved', false)->count(),
            'featured' => Testimonial::featured()->count(),
            'average_rating' => Testimonial::getAverageRating(),
            'rating_distribution' => Testimonial::getRatingDistribution(),
        ];

        // Top services by testimonials
        $topServices = Testimonial::with('service')
            ->approved()
            ->whereNotNull('service_id')
            ->selectRaw('service_id, COUNT(*) as count, AVG(rating) as avg_rating')
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'top_services' => $topServices
            ]
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:testimonials,id',
            'action' => 'required|in:approve,unapprove,feature,unfeature,delete',
            'service_id' => 'sometimes|nullable|exists:services,id'
        ]);

        $testimonials = Testimonial::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'approve':
                $testimonials->update(['is_approved' => true]);
                $message = 'Testimonials approved successfully';
                break;
            case 'unapprove':
                $testimonials->update(['is_approved' => false]);
                $message = 'Testimonials unapproved successfully';
                break;
            case 'feature':
                $testimonials->update(['is_featured' => true]);
                $message = 'Testimonials featured successfully';
                break;
            case 'unfeature':
                $testimonials->update(['is_featured' => false]);
                $message = 'Testimonials unfeatured successfully';
                break;
            case 'delete':
                // Delete photos first
                $testimonialsToDelete = $testimonials->get();
                foreach ($testimonialsToDelete as $testimonial) {
                    $this->deleteTestimonialPhoto($testimonial);
                }
                $testimonials->delete();
                $message = 'Testimonials deleted successfully';
                break;
        }

        if (isset($validated['service_id']) && $validated['action'] !== 'delete') {
            $testimonials->update(['service_id' => $validated['service_id']]);
            $message = 'Testimonials updated successfully';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'testimonials' => 'required|array',
            'testimonials.*.id' => 'required|exists:testimonials,id',
            'testimonials.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['testimonials'] as $testimonialData) {
            Testimonial::where('id', $testimonialData['id'])
                ->update(['sort_order' => $testimonialData['sort_order']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonials reordered successfully'
        ]);
    }

    private function handlePhotoUpload(Request $request, $existingTestimonial = null): array
    {
        $photoData = [];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048' // 2MB
            ]);

            // Delete old photo if updating
            if ($existingTestimonial && $existingTestimonial->photo_url) {
                $oldFilePath = public_path($existingTestimonial->photo_url);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/testimonials'), $fileName);
            
            $photoData['photo_url'] = '/uploads/testimonials/' . $fileName;
        }

        return $photoData;
    }

    private function deleteTestimonialPhoto($testimonial): void
    {
        if ($testimonial->photo_url) {
            $filePath = public_path($testimonial->photo_url);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}