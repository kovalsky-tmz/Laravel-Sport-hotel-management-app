<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Room extends Model
{
    use Notifiable;
	protected $primaryKey = 'room_number';
	protected $fillable=['room_number','max_guests','cost_night'];
	public $incrementing = false;

    public function hotel(){
    	return $this->hasMany('App\Hotel','room_number');
    }
    public function hotelcart(){
    	return $this->hasMany('App\Hotelcart','room_number');
    }
}
