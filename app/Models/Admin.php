<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    // protected $guard = 'admin';
    // protected $table = 'admins';
    protected $primaryKey = "admin_id";
    protected $fillable = [  
        'admin_id',
        'fname',
        'lname',
        'mname',
        'role',
        'address',
        'email',
        'admin_pic',
        'password'
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
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }
}