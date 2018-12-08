@extends('layout.master')
@include('layout.errors')
@section('content')

	<div class="row ">
		<div class="col-md-6 options" style="text-align: center;">
			<h2 style="padding-bottom: 1rem">Rezerwacja miejsca na obiekcie {{$name}}</h2>
			
			<div class="form-group col-md-8 center">
				@foreach($days as $day)
					<?php 
						//<?php $day_objects[$day]=DB::table('Objects')->where('object_name',$name)->where('day',$day)->get();
		            	if(array_search($day->day, $pl)==true){
		                	$day_pl=array_search($day->day, $pl);
		            	} 
	            	?>
					
					<span class="special_font">{{ucfirst($day_pl).':'}}</span>
					
					{{$day->hour_start}} - {{$day->hour_end}}
					<br>
				@endforeach
			</div>
			<form method="POST" action="/solo_reservation/reserve" >
				{{ csrf_field() }}
				<input type='hidden' name='name' value={{$name}}>
				<div class="form-group col-md-8 center" style="padding-top: 1rem">
					<label class='form-control-label' for="date_start">Wybierz dzień: </label>
					<input type="text" name="date_start" class="form-control datepicker"  readonly='true' id="dateajax" data-name="{{$name}}">
				</div>
				<div class="form-group col-md-8 center hours" style="margin-top: 20px">
				 

				</select> {{--JQUERY APPEND tutaj jest zamkniecie select, to są godziny początkowe do wyboru, po wyborze dnia --}}
				</div>
				<div class="form-group col-md-4 center" id="hours_amount" style="margin-top: 20px">

				</div>
				<div class="form-group col-md-7 center" id="hours" style="margin-top: 20px">

				</div>
				<div class="form-group col-md-7 center" id="guests_amount" style="margin-top: 20px">

				</div>
				<div class="form-group col-md-6 center" id="submit" style="margin-top: 20px">

				</div>
				<div class="form-group col-md-8 center" id="busy" style="margin-top: 10px">

				</div>
		
			</form>
		</div>
	</div>

<script src="/js/solo_reservation.js"></script>

@endsection

