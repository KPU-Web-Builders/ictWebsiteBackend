<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('can create partner with uploaded picture via picture key', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.png', 200, 200);

    $response = $this->postJson('/api/partners', [
        'name' => 'Acme Co',
        'picture' => $file,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'Acme Co');

    $path = $response->json('data.picture');
    expect($path)->not->toBeNull();
    // path is like /storage/partners/filename
    $relative = str_replace('/storage/', '', $path);
    expect(Storage::disk('public')->exists($relative))->toBeTrue();
});

test('can create partner with uploaded picture via image key', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('brand.jpg', 300, 100);

    $response = $this->postJson('/api/partners', [
        'name' => 'Brand X',
        'image' => $file,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'Brand X');

    $path = $response->json('data.picture');
    expect($path)->not->toBeNull();
    $relative = str_replace('/storage/', '', $path);
    expect(Storage::disk('public')->exists($relative))->toBeTrue();
});

test('can create partner with remote picture url', function () {
    $response = $this->postJson('/api/partners', [
        'name' => 'Remote Inc',
        'picture' => 'https://cdn.example.com/logo.webp',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'Remote Inc');
    $response->assertJsonPath('data.picture', 'https://cdn.example.com/logo.webp');
});
