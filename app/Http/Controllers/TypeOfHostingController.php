<?php

namespace App\Http\Controllers;

use App\Models\TypeOfHosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TypeOfHostingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TypeOfHosting::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $order = $request->get('order', 'asc') === 'desc' ? 'desc' : 'asc';
        $query->orderBy('name', $order)->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $typeOfHostings = $query->limit((int) $request->get('limit', 10))->get();
        } else {
            $typeOfHostings = $query->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $typeOfHostings,
        ]);
    }

    public function show($id): JsonResponse
    {
        $typeOfHosting = TypeOfHosting::find($id);

        if (!$typeOfHosting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of hosting not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $typeOfHosting,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $validated = $request->validate($rules);

        $fileData = $this->handleImageUpload($request);
        $data = array_merge($validated, $fileData);

        // If uploaded file exists, prefer it
        if (isset($fileData['image'])) {
            $data['image'] = $fileData['image'];
        }

        $typeOfHosting = TypeOfHosting::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Type of hosting created successfully',
            'data' => $typeOfHosting,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $typeOfHosting = TypeOfHosting::find($id);

        if (!$typeOfHosting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of hosting not found',
            ], 404);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $validated = $request->validate($rules);

        $fileData = $this->handleImageUpload($request, $typeOfHosting);
        $data = array_merge($validated, $fileData);

        if (isset($fileData['image'])) {
            $data['image'] = $fileData['image'];
        }

        $typeOfHosting->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Type of hosting updated successfully',
            'data' => $typeOfHosting->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $typeOfHosting = TypeOfHosting::find($id);

        if (!$typeOfHosting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type of hosting not found',
            ], 404);
        }

        $this->deleteImageFile($typeOfHosting->image);

        $typeOfHosting->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Type of hosting deleted successfully',
        ]);
    }

    private function handleImageUpload(Request $request, ?TypeOfHosting $existing = null): array
    {
        $data = [];

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if ($existing && !empty($existing->image)) {
                $this->deleteImageFile($existing->image);
            }

            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            Storage::disk('public')->putFileAs('type_of_hostings', $file, $fileName);
            $data['image'] = '/storage/type_of_hostings/' . $fileName;
        }

        return $data;
    }

    private function deleteImageFile(?string $path): void
    {
        if (!$path) return;

        if (str_starts_with($path, '/storage/')) {
            $relative = substr($path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
    }
}
