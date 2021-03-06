<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'isTeacher'
    ];
    public function isTeacher(): bool
    {
        if($this->isTeacher=='1'){
            return true;
        }
        return false;
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];
    public function classes(){
      return $this->belongsToMany(MClass::class, 'students_classes');
    }
    public function attempts(){
      return $this->belongsToMany(Attempt::class, 'attempts');
    }

}
