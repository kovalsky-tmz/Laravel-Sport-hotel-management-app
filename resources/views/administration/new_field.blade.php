@extends('layout.master')
@if (session()->has('information'))
  <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif
@include('layout.errors')
@section('content')

	<div class="row ">
		<div class="col-md-5 options" style="text-align: center;">
			<h2 style="padding-bottom: 3rem">Dodawanie nowego obszaru do obiektu {{str_replace('_', ' ', $name)}}</h2>
			<form method="POST" action="/new_field/" >
			{{ csrf_field() }}
			<input type="hidden" name='name' value={{$name}} id='name'>
			 <div class="form-group">
			    <label for="field_type">Nazwa/Rodzaj/Typ obszaru</label>
			    <input type="text" class="form-control" name="field_type" required id="field_type" aria-describedby="field_type">
			 </div>
			 <div class="form-group">
			    <label for="cost">Koszt za wynajem</label>
			    <input type="text" class="form-control" required name="cost" id="cost" pattern="\d*">
			 </div>
			 <div class="form-group">
			    <label for="description">Opis</label>
			    <input type="text" class="form-control" required name="description" id="description">
			 </div>
			 <div class="form-group visible">
				<label for="hour_start">Od której godziny</label>
				<input type="text" class="form-control timepicker" required name="hour_start" id="hour_start">
			</div>
			<div class="form-group visible">
				<label for="hour_end">Do której godziny</label>
				<input type="text" class="form-control timepicker" required name="hour_end" id="hour_end">
			</div>	
			<div class="col-md-12 offset-0 center visible">
  				<input type="checkbox" name="day[0]" class="day" value="monday"> Poniedziałek
  				<input type="checkbox" name="day[1]" class="day" value="tuesday "> Wtorek
  				<input type="checkbox" name="day[2]" class="day" value="wednesday "> Środa 
  				<input type="checkbox" name="day[3]" class="day" value="thursday "> Czwartek<br>
  				<input type="checkbox" name="day[4]" class="day" value="friday "> Piątek
  				<input type="checkbox" name="day[5]" class="day" value="saturday "> Sobota
  				<input type="checkbox" name="day[6]" class="day" value="sunday "> Niedziela<br><br>
  			</div>
  			<div class="col-md-12 offset-0 center" >
				<input type="checkbox" name="day[7]" class="visible" id="everyday" value="everyday"> <span class='visible'>Codziennie</span><br><br>
				<button type="submit" class="btn btn-lg btn-primary">Stwórz</button>
			</div>
				</form>
		</div>
	</div>


<script>
$(document).ready(function() { 
	$('.day').on('click',function(){
		$('#everyday').attr('checked', false);	
		if ($('.day:checked').length == $('.day').length) {
			$('.day').attr('checked', false);	
			$('#everyday').prop('checked', true);
		}
	})
	$('#everyday').on('click',function(){
		$('.day').attr('checked', false);	
	})

	$('.timepicker').timepicker({
	    timeFormat: 'HH:mm',
	    interval: 30,
	    minTime: '08',
	    maxTime: '20:00',
	   
	    startTime: '08:00',
	    dynamic: false,
	    dropdown: true,
	    scrollbar: true
	});
});
</script>
@endsection

