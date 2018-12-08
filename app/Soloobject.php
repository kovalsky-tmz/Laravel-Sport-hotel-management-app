<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Soloobject extends Model
{
    use Notifiable;
	protected $primaryKey = 'id';
	protected $fillable=['id','object_name','max_guests','system','sequence_time','cost_hour','break_time','hour_start','hour_end','day'];
	public $incrementing = true;
}
