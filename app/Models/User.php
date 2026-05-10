<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'profile_photo_path'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $guarded = ['id'];
    protected $appends = ['is_loyal'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLoyaltyCustomer(): bool
    {
        return $this->cached_orders_count >= 100;
    }

    protected function getIsLoyalAttribute(): bool
    {
        return $this->isLoyaltyCustomer();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function cartProducts()
    {
        return $this->belongsToMany(Product::class, 'carts')
            ->withPivot('quantity')
            ->wherePivot('deleted_at', null);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->where('deleted_at', null);
    }

    public function profilePhoto()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
