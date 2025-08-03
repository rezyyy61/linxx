<?php

namespace App\Models;

use App\Models\Book\Book;
use App\Models\Book\BookReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'slug',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = (string) Str::uuid();

            $baseSlug = Str::slug($user->name);
            $slug = $baseSlug;
            $i = 1;

            while (static::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $user->slug = $slug;
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function politicalProfile(): HasOne
    {
        return $this->hasOne(PoliticalProfile::class);
    }

    public function getAvatarAttribute()
    {
        return $this->politicalProfile?->logo_path
            ? asset('storage/' . $this->politicalProfile->logo_path)
            : null;
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'user_books')
            ->withPivot(['is_downloaded', 'is_read'])
            ->withTimestamps();
    }

    public function uploadedBooks()
    {
        return $this->hasMany(Book::class, 'uploaded_by');
    }

    public function bookReviews()
    {
        return $this->hasMany(BookReview::class);
    }

}
