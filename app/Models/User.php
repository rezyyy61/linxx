<?php

namespace App\Models;

use App\Models\Book\Book;
use App\Models\Book\BookReview;
use App\Models\Event\Event;
use App\Models\Event\EventRsvp;
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
        'is_verified',
        'verification_code',
        'verification_expires_at'
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
            'verification_expires_at' => 'datetime',
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function politicalProfile(): HasOne
    {
        return $this->hasOne(PoliticalProfile::class);
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

    public function createdEvents()
    {
        return $this->morphMany(Event::class, 'creator');
    }

    public function eventRsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_rsvps')
            ->withPivot('status', 'checked_in_at')
            ->withTimestamps();
    }

    public function isParty(): bool
    {
        return $this->role === 'party';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


}
