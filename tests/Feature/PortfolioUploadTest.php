<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('portfolio create accepts featured_image upload', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('featured.jpg', 800, 600);

    $res = $this->postJson('/api/portfolio', [
        'title' => 'My Project',
        'featured_image' => $file,
    ]);

    $res->assertCreated();
    $path = $res->json('data.featured_image');
    expect($path)->not->toBeNull();
    $relative = str_replace('/storage/', '', $path);
    expect(Storage::disk('public')->exists($relative))->toBeTrue();
});

test('portfolio create accepts multiple gallery_images upload', function () {
    Storage::fake('public');
    $files = [
        UploadedFile::fake()->image('g1.png', 400, 300),
        UploadedFile::fake()->image('g2.png', 400, 300),
    ];

    $res = $this->postJson('/api/portfolio', [
        'title' => 'Gallery Project',
        'gallery_images' => $files,
    ]);

    $res->assertCreated();
    $paths = $res->json('data.gallery_images');
    expect($paths)->toBeArray()->and(count($paths))->toBe(2);
    foreach ($paths as $p) {
        $relative = str_replace('/storage/', '', $p);
        expect(Storage::disk('public')->exists($relative))->toBeTrue();
    }
});

