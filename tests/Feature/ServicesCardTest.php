<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('can create service card with uploaded picture via picture key', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('icon.png', 120, 120);

    $res = $this->postJson('/api/services-cards', [
        'name' => 'Card A',
        'description' => 'Short description',
        'picture' => $file,
    ]);

    $res->assertCreated();
    $res->assertJsonPath('data.name', 'Card A');
    $path = $res->json('data.picture');
    $rel = str_replace('/storage/', '', $path);
    expect(Storage::disk('public')->exists($rel))->toBeTrue();
});

test('can create service card with uploaded picture via image key', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('icon2.jpg', 100, 60);

    $res = $this->postJson('/api/services-cards', [
        'name' => 'Card B',
        'image' => $file,
    ]);

    $res->assertCreated();
    $path = $res->json('data.picture');
    $rel = str_replace('/storage/', '', $path);
    expect(Storage::disk('public')->exists($rel))->toBeTrue();
});

test('can create service card with remote picture url', function () {
    $res = $this->postJson('/api/services-cards', [
        'name' => 'Card C',
        'picture' => 'https://cdn.example.com/icon.webp',
    ]);

    $res->assertCreated();
    $res->assertJsonPath('data.picture', 'https://cdn.example.com/icon.webp');
});

