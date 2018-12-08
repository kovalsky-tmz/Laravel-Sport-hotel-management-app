<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Hotelcart extends Model
{
    use Notifiable;
	protected $primaryKey = 'id';
	protected $dates = ['reservation_start','reservation_end'];
	protected $fillable=['cost_night','room_number','guests_amount','reservation_start','reservation_end','user_id'];
	public $incrementing = true;

    public function user()
    {
    	return $this->belongsTo('App\User','user_id');
    }

    public function room()
    {
    	return $this->belongsTo('App\Room','room_number');
    }


}
