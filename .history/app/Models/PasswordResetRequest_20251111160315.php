<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetRequest extends Model
{
    protected $table = 'password_reset_codes'; // atau 'password_reset_requests'

    protected $fillable = [
        'email',
        'code',
        'status',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Check if code is expired
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    // Mark as used
    public function markAsUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => Carbon::now(),
        ]);
    }

    // Mark as expired
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

        public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

}
