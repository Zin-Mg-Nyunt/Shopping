<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('product thumbnail accessor wraps relative storage paths', function () {
    $product = Product::factory()->create([
        'thumbnail' => 'thumbnails/x.jpg',
    ]);

    expect($product->thumbnail)->toBe(Storage::url('thumbnails/x.jpg'));
});

test('product thumbnail accessor leaves absolute http urls unchanged', function () {
    $url = 'https://example.com/a.jpg';

    $product = Product::factory()->create([
        'thumbnail' => $url,
    ]);

    expect($product->thumbnail)->toBe($url);
});
