<?php

namespace App\Http\Controllers;

use App\Models\Partners;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Partners::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $order = $request->get('order', 'asc') === 'desc' ? 'desc' : 'asc';
        $query->orderBy('name', $order)->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $partners = $query->limit((int) $request->get('limit', 10))->get();
        } else {
            $partners = $query->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $partners,
        ]);
    }

    public function show($id): JsonResponse
    {
        $partner = Partners::find($id);

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Partner not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $partner,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:150',
            'picture' => 'nullable|string|max:255',
        ];

        if ($request->hasFile('picture')) {
            $rules['picture'] = 'image|mimes:jpeg,png,jpg,gif,webp';
        }
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,webp';
        }

        $validated = $request->validate($rules);

        $fileData = $this->handlePictureUpload($request);
        $data = array_merge($validated, $fileData);

        // If both provided, prefer uploaded file path
        if (isset($fileData['picture'])) {
            $data['picture'] = $fileData['picture'];
        }

        $partner = Partners::create([
            'name' => $data['name'],
            'picture' => $data['picture'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Partner created successfully',
            'data' => $partner,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $partner = Partners::find($id);

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Partner not found',
            ], 404);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:150',
            'picture' => 'nullable|string|max:255',
        ];
        if ($request->hasFile('picture')) {
            $rules['picture'] = 'image|mimes:jpeg,png,jpg,gif,webp';
        }
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,webp';
        }
        $validated = $request->validate($rules);

        $fileData = $this->handlePictureUpload($request, $partner);
        $data = array_merge($validated, $fileData);

        if (isset($fileData['picture'])) {
            $data['picture'] = $fileData['picture'];
        }

        $partner->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Partner updated successfully',
            'data' => $partner->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $partner = Partners::find($id);

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Partner not found',
            ], 404);
        }

        $this->deletePictureFile($partner->picture);

        $partner->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Partner deleted successfully',
        ]);
    }

    private function handlePictureUpload(Request $request, ?Partners $existing = null): array
    {
        $data = [];
        $file = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
        }

        if ($file) {
            if ($existing && !empty($existing->picture)) {
                $this->deletePictureFile($existing->picture);
            }
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            Storage::disk('public')->putFileAs('partners', $file, $fileName);
            $data['picture'] = '/storage/partners/' . $fileName;
        }
        return $data;
    }

    private function deletePictureFile(?string $path): void
    {
        if (!$path) return;
        if (str_starts_with($path, '/storage/')) {
            $relative = substr($path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
    }
}
