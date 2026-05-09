<?php

namespace App\Models;

use Database\Factories\PromoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    /** @use HasFactory<PromoFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
