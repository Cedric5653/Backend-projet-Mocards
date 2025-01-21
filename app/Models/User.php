<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nom', 'prenom', 'email', 'password_hash', 'role_id'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'medecin_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMedecin()
    {
        return $this->role === 'medecin';
    }

    public function isInfirmier()
    {
        return $this->role === 'infirmier';
    }

    public function isSecouriste()
    {
        return $this->role === 'secouriste';
    }


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
