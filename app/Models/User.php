<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'profile_slug',
        'email',
        'password',
        'is_banned',
        'banned_at',
        'ban_reason',
        'is_admin',
        'last_login_ip',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_banned' => 'boolean',
            'is_admin' => 'boolean',
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Boot method to generate profile slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->profile_slug)) {
                $user->profile_slug = Str::slug($user->username);
            }
        });
    }

        /**
     * Check if user is banned
     */
    public function isBanned(): bool
    {
        return $this->is_banned;
    }

     /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }


    /**
     * Get profile URL
     */
    public function getProfileUrlAttribute(): string
    {
        return route('profile.show', $this->profile_slug);
    }




}
