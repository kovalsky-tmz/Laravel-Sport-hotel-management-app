<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    
    protected $primaryKey = 'user_id';
    public $incrementing=false;
    protected $fillable = [
       'user_id', 'first_name','last_name','phone','city', 'email', 'password','confirmation_code','confirmed',
    ];
    protected $guarded=[
        'role','password',
    ];
    protected $hidden = [
         'remember_token',
    ];
    
    public function reservation(){
        return $this->hasMany('App\Reservation','user_id');
    }
    public function hasRole($role)
    {
        return User::where('role', $role)->get();
    }

    public function getJWTIdentifier()
        {
            return $this->getKey();
        }
    public function getJWTCustomClaims()
        {
            return [];
        }

    public static function all_users(){
        return DB::table('users')->get();
    }
}
