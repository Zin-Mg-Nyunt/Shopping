<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{

    protected $guarded = ['id'];

    public function imageable()
    {
        return $this->morphTo();
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            get: static fn (?string $value) => blank($value)
                ? null
                : (str_starts_with($value, 'http')
                    ? $value
                    : Storage::url($value))
        );
    }
}
