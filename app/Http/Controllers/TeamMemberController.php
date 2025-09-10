<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamMemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TeamMember::query();

        // Filter by active status
        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // Filter by verified status
        if ($request->has('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        // Filter by role
        if ($request->has('role')) {
            $query->byRole($request->role);
        }

        // Search by name or role
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }

        // Apply ordering
        $query->ordered();

        $members = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $members
        ]);
    }

    public function show($id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team member not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $member
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'joined_date' => 'nullable|date'
        ]);

        // Handle photo upload
        $photoData = $this->handlePhotoUpload($request);
        $validated = array_merge($validated, $photoData);

        $member = TeamMember::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Team member created successfully',
            'data' => $member
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team member not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'role' => 'sometimes|required|string|max:100',
            'bio' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'joined_date' => 'nullable|date'
        ]);

        // Handle photo upload
        $photoData = $this->handlePhotoUpload($request, $member);
        $validated = array_merge($validated, $photoData);

        $member->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Team member updated successfully',
            'data' => $member->fresh()
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team member not found'
            ], 404);
        }

        // Delete associated photo
        $this->deleteMemberPhoto($member);

        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Team member deleted successfully'
        ]);
    }

    public function toggleActive($id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team member not found'
            ], 404);
        }

        $member->update(['is_active' => !$member->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Team member status updated successfully',
            'data' => $member->fresh()
        ]);
    }

    public function toggleVerified($id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team member not found'
            ], 404);
        }

        $member->update(['is_verified' => !$member->is_verified]);

        return response()->json([
            'status' => 'success',
            'message' => 'Team member verification status updated successfully',
            'data' => $member->fresh()
        ]);
    }

    public function getByRole($role): JsonResponse
    {
        $members = TeamMember::byRole($role)->active()->ordered()->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => $role,
                'members' => $members,
                'count' => $members->count()
            ]
        ]);
    }

    private function handlePhotoUpload(Request $request, $existingMember = null): array
    {
        $photoData = [];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072' // 3MB
            ]);

            // Delete old photo if updating
            if ($existingMember && $existingMember->photo_url) {
                $oldFilePath = public_path($existingMember->photo_url);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/team'), $fileName);
            
            $photoData['photo_url'] = '/uploads/team/' . $fileName;
        }

        return $photoData;
    }

    private function deleteMemberPhoto($member): void
    {
        if ($member->photo_url) {
            $filePath = public_path($member->photo_url);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}