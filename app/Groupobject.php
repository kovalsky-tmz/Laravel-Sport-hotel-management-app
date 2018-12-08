<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Groupobject extends Model
{
    use Notifiable;
	protected $primaryKey = 'id';
	protected $fillable=['id','object_name','system','sequence_time','break_time'];
	public $incrementing = true;
}
