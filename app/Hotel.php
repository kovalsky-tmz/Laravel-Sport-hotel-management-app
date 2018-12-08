<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Hotel extends Model
{
	use Notifiable;
	protected $primaryKey = 'id';
	protected $dates = ['reservation_start','reservation_end'];
	protected $fillable=['reservation_id','room_number','guests_amount','reservation_start','reservation_end','cost','description'];
	public $incrementing = true;

    public function room()
    {
    	return $this->belongsTo('App\Room','room_number');
    }

    public function reservation()
    {
    	return $this->belongsTo('App\Reservation','reservation_id');
    }
}
