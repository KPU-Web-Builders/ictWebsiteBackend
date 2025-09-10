<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Portfolio::with('service.category');

        // Filter by published status
        if ($request->has('published')) {
            if ($request->boolean('published')) {
                $query->published();
            } else {
                $query->where('is_published', false);
            }
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Filter by service
        if ($request->has('service_id')) {
            $query->byService($request->service_id);
        }

        // Search by title or client name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('client_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by year
        if ($request->has('year')) {
            $query->whereYear('project_date', $request->year);
        }

        // Limit for recent items
        if ($request->has('recent')) {
            $query->recent($request->get('recent', 10));
        } else {
            // Apply default ordering
            $query->ordered();
        }

        $portfolios = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $portfolios
        ]);
    }

    public function show($slug): JsonResponse
    {
        $portfolio = Portfolio::with('service.category')->where('slug', $slug)->first();

        if (!$portfolio) {
            return response()->json([
                'status' => 'error',
                'message' => 'Portfolio item not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $portfolio
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:portfolio,slug',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:100',
            'project_url' => 'nullable|url|max:255',
            'service_id' => 'nullable|exists:services,id',
            'technologies_used' => 'nullable|array',
            'technologies_used.*' => 'string|max:50',
            'project_date' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean'
        ]);

        // Handle file uploads
        $validated = array_merge($validated, $this->handleFileUploads($request));

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Ensure slug uniqueness
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Portfolio::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $portfolio = Portfolio::create($validated);
        $portfolio->load('service.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Portfolio item created successfully',
            'data' => $portfolio
        ], 201);
    }

    public function update(Request $request, $slug): JsonResponse
    {
        $portfolio = Portfolio::where('slug', $slug)->first();

        if (!$portfolio) {
            return response()->json([
                'status' => 'error',
                'message' => 'Portfolio item not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:200',
            'slug' => ['sometimes', 'nullable', 'string', 'max:200', Rule::unique('portfolio')->ignore($portfolio->id)],
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:100',
            'project_url' => 'nullable|url|max:255',
            'service_id' => 'nullable|exists:services,id',
            'technologies_used' => 'nullable|array',
            'technologies_used.*' => 'string|max:50',
            'project_date' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean'
        ]);

        // Handle file uploads
        $fileData = $this->handleFileUploads($request, $portfolio);
        $validated = array_merge($validated, $fileData);

        // Auto-generate slug if title changed but slug not provided
        if (isset($validated['title']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure slug uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Portfolio::where('slug', $validated['slug'])->where('id', '!=', $portfolio->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $portfolio->update($validated);
        $portfolio->load('service.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Portfolio item updated successfully',
            'data' => $portfolio->fresh(['service.category'])
        ]);
    }

    public function destroy($slug): JsonResponse
    {
        $portfolio = Portfolio::where('slug', $slug)->first();

        if (!$portfolio) {
            return response()->json([
                'status' => 'error',
                'message' => 'Portfolio item not found'
            ], 404);
        }

        // Delete associated files
        $this->deletePortfolioFiles($portfolio);

        $portfolio->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Portfolio item deleted successfully'
        ]);
    }

    public function toggleFeatured($slug): JsonResponse
    {
        $portfolio = Portfolio::where('slug', $slug)->first();

        if (!$portfolio) {
            return response()->json([
                'status' => 'error',
                'message' => 'Portfolio item not found'
            ], 404);
        }

        $portfolio->update(['is_featured' => !$portfolio->is_featured]);
        $portfolio->load('service.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Portfolio featured status updated successfully',
            'data' => $portfolio->fresh(['service.category'])
        ]);
    }

    public function togglePublished($slug): JsonResponse
    {
        $portfolio = Portfolio::where('slug', $slug)->first();

        if (!$portfolio) {
            return response()->json([
                'status' => 'error',
                'message' => 'Portfolio item not found'
            ], 404);
        }

        $portfolio->update(['is_published' => !$portfolio->is_published]);
        $portfolio->load('service.category');

        return response()->json([
            'status' => 'success',
            'message' => 'Portfolio published status updated successfully',
            'data' => $portfolio->fresh(['service.category'])
        ]);
    }

    private function handleFileUploads(Request $request, $existingPortfolio = null): array
    {
        $fileData = [];

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $request->validate([
                'featured_image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120' // 5MB
            ]);

            // Delete old featured image if updating
            if ($existingPortfolio && $existingPortfolio->featured_image) {
                $oldFilePath = public_path($existingPortfolio->featured_image);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file = $request->file('featured_image');
            $fileName = time() . '_featured_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/portfolio'), $fileName);
            
            $fileData['featured_image'] = '/uploads/portfolio/' . $fileName;
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $request->validate([
                'gallery_images' => 'array|max:10',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);

            // Delete old gallery images if updating
            if ($existingPortfolio && $existingPortfolio->gallery_images) {
                foreach ($existingPortfolio->gallery_images as $oldImage) {
                    $oldFilePath = public_path($oldImage);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            }

            $galleryImages = [];
            foreach ($request->file('gallery_images') as $index => $file) {
                $fileName = time() . '_gallery_' . $index . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/portfolio'), $fileName);
                $galleryImages[] = '/uploads/portfolio/' . $fileName;
            }
            
            $fileData['gallery_images'] = $galleryImages;
        }

        return $fileData;
    }

    private function deletePortfolioFiles($portfolio): void
    {
        // Delete featured image
        if ($portfolio->featured_image) {
            $filePath = public_path($portfolio->featured_image);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete gallery images
        if ($portfolio->gallery_images && is_array($portfolio->gallery_images)) {
            foreach ($portfolio->gallery_images as $image) {
                $filePath = public_path($image);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }
}