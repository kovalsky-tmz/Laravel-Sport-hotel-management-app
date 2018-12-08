@extends('layout.master')
@include('layout.errors')
@section('content')


	<div class="col-md-9 options" style='padding-bottom: 3rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;'>
		<div class="row ">
			<h2 style="padding-left: 1rem;margin-top: 0rem">Tworzenie nowego obiektu sportowego</h2>
			<div class="col-md-6 options">
				
				<form method="POST" action="/new_object" novalidate >
					{{ csrf_field() }}
					<div class="form-group" ;">
						<label for="system_type"><b>System Rezerwacji:</b></label>
						<select class="form-control" name="system_type" id="system_type">
							<option value='individual'>Indywidualny</option>
							<option value='group'>Drużynowy/Grupowy</option>
						</select>
					</div>
					<div class="form-group">
					    <label for="object_name">Nazwa Obiektu</label>
					    <input type="text" class="form-control" required name="object_name" id="object_name">
					</div>
					<div class="form-group visible">
					    <label for="cost_hour">Kwota za jedno wejście</label>
					    <input type="text" class="form-control" required name="cost_hour" id="cost_hour" pattern="\d*">
					</div>
					<div class="form-group visible">
					    <label for="max_guests">Maksymalna ilość osób na obiekcie</label>
					    <input type="text" class="form-control" name="max_guests" id="max_guests" pattern="\d*">
					</div>
					
					
			</div>
			<div class="col-md-6 options" >
					<div class="form-group">
					    <label for="sequence_time">Czas trwania jednej rezerwacji [min]</label>
					    <input type="text" class="form-control" name="sequence_time" id="sequence_time" pattern="\d*">
					</div>
					<div class="form-group">
					    <label for="break_time">Czas przerwy między wejściami [min]</label>
					    <input type="text" class="form-control" name="break_time" id="break_time" pattern="\d*">
					</div>
					<div class="form-group visible">
					    <label for="hour_start">Od której godziny </label>
					    <input type="text" class="form-control timepicker" name="hour_start" id="hour_start">
					</div>
					<div class="form-group visible">
					    <label for="hour_end">Do której godziny </label>
					    <input type="text" class="form-control timepicker" name="hour_end" id="hour_end">
					</div>	  
			</div>
			
			<div class="col-md-12 offset-1 center visible">
  				<input type="checkbox" name="day[0]" class="day" value="monday"> Poniedziałek
  				<input type="checkbox" name="day[1]" class="day" value="tuesday "> Wtorek
  				<input type="checkbox" name="day[2]" class="day" value="wednesday "> Środa 
  				<input type="checkbox" name="day[3]" class="day" value="thursday "> Czwartek
  				<input type="checkbox" name="day[4]" class="day" value="friday "> Piątek
  				<input type="checkbox" name="day[5]" class="day" value="saturday "> Sobota
  				<input type="checkbox" name="day[6]" class="day" value="sunday "> Niedziela<br><br>
  			</div>
  			<div class="col-md-12 offset-5 center" >
				<input type="checkbox" name="day[7]" class="visible" value="everyday" id="everyday"> <span class='visible'>Codziennie</span><br><br>
				<button type="submit" class="btn btn-lg btn-primary">Stwórz</button>
			</div>
				</form>
		</div>
	</div>

<script>
$(document).ready(function() { 
	$('#system_type').on('change',function(){
		var $test=$(this).val();
		if($test=='group'){
			$(".visible").attr('hidden','hidden');

		}else if($test=='individual'){
			$(".visible").removeAttr('hidden','hidden');
		}
	});
	$('.day').on('click',function(){
		$('#everyday').attr('checked', false);	
		if ($('.day:checked').length == $('.day').length) {
			$('.day').attr('checked', false);	
			$('#everyday').prop('checked', true);
		}
	});
	$('#everyday').on('click',function(){
		$('.day').attr('checked', false);	
	});
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
{{-- <script>
$(document).ready(function() { 
	$('#system_type').on('change',function(){
		var $test=$('#system_type').val();
		if($test=='group'){
			$("#max_guests").attr('disabled','disabled');
			$("#max_guests").val('Nie dotyczy');
			$("#cost_hour").attr('disabled','disabled');
			$("#cost_hour").val('Zależne od wybranego boiska');
		}else if($test=='individual'){
			$('#max_guests').removeAttr('disabled');
			$('#cost_hour').removeAttr('disabled');
			$("#max_guests").val('');
			$("#cost_hour").val('');
		}
	})
});
</script> --}}
@endsection

