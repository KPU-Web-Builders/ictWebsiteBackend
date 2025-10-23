<?php

namespace App\Http\Controllers;

use App\Models\services_card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicesCardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = services_card::query();

        if ($request->filled('search')) {
            $search = (string) $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $order = $request->get('order', 'asc') === 'desc' ? 'desc' : 'asc';
        $query->orderBy('name', $order)->orderBy('id', 'asc');

        $cards = $request->filled('limit')
            ? $query->limit((int) $request->get('limit', 10))->get()
            : $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $cards,
        ]);
    }

    public function show($id): JsonResponse
    {
        $card = services_card::find($id);

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service card not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $card,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
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
        if (isset($fileData['picture'])) {
            $data['picture'] = $fileData['picture'];
        }

        $card = services_card::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'picture' => $data['picture'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Service card created successfully',
            'data' => $card,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $card = services_card::find($id);
        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service card not found',
            ], 404);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'picture' => 'nullable|string|max:255',
        ];
        if ($request->hasFile('picture')) {
            $rules['picture'] = 'image|mimes:jpeg,png,jpg,gif,webp';
        }
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,webp';
        }
        $validated = $request->validate($rules);

        $fileData = $this->handlePictureUpload($request, $card);
        $data = array_merge($validated, $fileData);
        if (isset($fileData['picture'])) {
            $data['picture'] = $fileData['picture'];
        }

        $card->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Service card updated successfully',
            'data' => $card->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $card = services_card::find($id);

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service card not found',
            ], 404);
        }

        $this->deletePictureFile($card->picture);
        $card->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Service card deleted successfully',
        ]);
    }

    private function handlePictureUpload(Request $request, ?services_card $existing = null): array
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
            Storage::disk('public')->putFileAs('services_cards', $file, $fileName);
            $data['picture'] = '/storage/services_cards/' . $fileName;
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

