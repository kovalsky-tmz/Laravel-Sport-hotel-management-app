<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class Reservation extends Model
{
	use Notifiable;
	protected $primaryKey = 'reservation_id';
	protected $fillable=['reservation_id','reservation_type','user_id','status','comment'];
	public $incrementing = true;
    public function user()
    {
    	return $this->belongsTo('App\User','user_id');
    }

    public function gym(){
    	return $this->hasMany('App\Gym','reservation_id');
    }

    public function hotel(){
        return $this->hasMany('App\Hotel','reservation_id');
    }

    
    public static function carts($table){
        return DB::table($table)->where('user_id',Auth::id())->get();
    }
    public static function carts_count($table){
        return DB::table($table)->where('user_id',Auth::id())->count();
    }
}
