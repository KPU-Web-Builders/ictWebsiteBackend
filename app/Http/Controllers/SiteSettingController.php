<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SiteSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = SiteSetting::all();
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    public function show(string $key): JsonResponse
    {
        $setting = SiteSetting::where('setting_key', $key)->first();

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $setting
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'setting_key' => 'required|string|max:50|unique:site_settings,setting_key',
            'setting_type' => ['required', Rule::in(['text', 'image', 'json', 'boolean'])],
            'description' => 'nullable|string'
        ]);

        $settingValue = $this->handleSettingValue($request);

        $setting = SiteSetting::create([
            'setting_key' => $request->setting_key,
            'setting_value' => $settingValue,
            'setting_type' => $request->setting_type,
            'description' => $request->description,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Setting created successfully',
            'data' => $setting
        ], 201);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $setting = SiteSetting::where('setting_key', $key)->first();

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting not found'
            ], 404);
        }

        $request->validate([
            'setting_type' => ['sometimes', Rule::in(['text', 'image', 'json', 'boolean'])],
            'description' => 'nullable|string'
        ]);

        $settingValue = $this->handleSettingValue($request, $setting);

        $setting->update([
            'setting_value' => $settingValue,
            'setting_type' => $request->setting_type ?? $setting->setting_type,
            'description' => $request->description ?? $setting->description,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Setting updated successfully',
            'data' => $setting
        ]);
    }

    public function destroy(string $key): JsonResponse
    {
        $setting = SiteSetting::where('setting_key', $key)->first();

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setting not found'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Setting deleted successfully'
        ]);
    }

    private function handleSettingValue(Request $request, $existingSetting = null): string
    {
        $settingType = $request->setting_type ?? $existingSetting?->setting_type;

        // Handle file upload for image type
        if ($settingType === 'image' && $request->hasFile('file')) {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
            ]);

            // Delete old file if updating
            if ($existingSetting && $existingSetting->setting_value) {
                $oldFilePath = public_path($existingSetting->setting_value);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/settings'), $fileName);
            
            return '/uploads/settings/' . $fileName;
        }

        // Handle regular setting_value for non-image types or when no file is uploaded
        if ($request->has('setting_value')) {
            return $request->setting_value;
        }

        // If updating and no new value provided, keep existing value
        if ($existingSetting) {
            return $existingSetting->setting_value;
        }

        // For new settings without file upload, setting_value is required
        $request->validate(['setting_value' => 'required']);
        return $request->setting_value;
    }
}