<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
    'email',
    'password',
    'role',
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
        ];
    }

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/profile_pictures/' . $this->profile_picture);
        }
        
        return asset('assets/img/theme/default-avatar.png');
    }

    /**
     * Get the user's initials for avatar fallback.
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Get the user's first name.
     */
    public function getFirstNameAttribute()
    {
        return explode(' ', $this->name)[0];
    }

    /**
     * Check if user has profile picture.
     */
    public function hasProfilePicture()
    {
        return !empty($this->profile_picture) && Storage::disk('public')->exists('profile_pictures/' . $this->profile_picture);
    }

    /**
     * Delete user's profile picture from storage.
     */
    public function deleteProfilePicture()
    {
        if ($this->profile_picture) {
            $imagePath = 'profile_pictures/' . $this->profile_picture;
            
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            $this->profile_picture = null;
            $this->save();
            
            return true;
        }
        
        return false;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Delete profile picture when user is deleted
        static::deleting(function ($user) {
            $user->deleteProfilePicture();
        });
    }
}
