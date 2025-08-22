<?php

namespace App\Models;
use App\Enums\UserRole;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
protected $fillable = [
    'name', 'email', 'password', 'phone', 'role', 'is_active', 'is_locked', 'lock_reason', 'address'
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
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
    public function markEmailAsVerified()
    {
        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    // function của infouser
    public function addresses()
    {
        return $this->hasOne(addresses::class);
    }
    public function isAdmin()
    {
        // return $this->role === UserRole::ADMIN->value;
        return $this->hasOne(addresses::class); // nếu mỗi user chỉ có 1 địa chỉ
    }
    
}
