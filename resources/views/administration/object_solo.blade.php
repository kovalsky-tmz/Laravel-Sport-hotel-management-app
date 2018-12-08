@extends('layout.master')
@include('layout.errors')
@section('content')
@if (session()->has('information'))
      <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif

	<div class="col-md-6 center" style='padding-bottom: 1rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;'>
		<h3 style="text-align:left; padding: 1rem; margin-bottom:2rem; border-bottom: 1px solid #eee";>Informacje o obiekcie</h3>
		<div class="form-group">
			Nazwa obiektu: <span class='special_font'> {{str_replace('_', ' ', $name)}}</span>
		</div>
		<div class="form-group">
			System:<span class='special_font'> Indywidualny</span>
		</div>
		<div class="form-group">
			Koszt za wejście @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#cost">Edytuj</a>]@endif:<span class='special_font'> {{$object->cost_hour}}</span>
		</div>
		<div class="form-group">
			Maksymalna liczba gości @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#maxGuests">Edytuj</a>]@endif:<span class='special_font'> {{$object->max_guests}}</span>
		</div>
		<div class="form-group">
			Czas dla jednego wejścia @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#time">Edytuj</a>]@endif : <span class='special_font'>{{$object->sequence_time}}</span>
		</div>
		
		<div class="form-group">
			Przerwa @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#breakModal">Edytuj</a>]@endif: <span class='special_font'>{{$object->break_time}}</span>
		</div>
		<div class="form-group">
			Ilość nieaktywnych rezerwacji  [<a href='#' data-toggle="modal" data-target="#ShowReservationsInactive">Użytkownicy</a>]: <span class='special_font'>{{$reserv_amount_inactive}}</span>
		</div>
		<div class="form-group">
			Ilość aktywnych rezerwacji  [<a href='#' data-toggle="modal" data-target="#ShowReservationsActive">Użytkownicy</a>]: <span class='special_font'>{{$reserv_amount_active}}</span>
		</div>
		<div class="form-group">
			Akcje administracyjne  [<a href='#' data-toggle="modal" data-target="#ShowEvent">Zarząd</a>]: <span class='special_font'>{{$reserv_amount_event}}</span>
		</div>
		<h3 style="text-align:left; padding: 1rem; margin-bottom:2rem;margin-top:2rem ;border-bottom: 1px solid #eee";>Czynne w dni</h3>
		@if($days!='null')
			@foreach($days as $day)
				<?php 
					//<?php $day_objects[$day]=DB::table('Objects')->where('object_name',$name)->where('day',$day)->get();
	            	if(array_search($day->day, $pl)==true){
	                	$day_pl=array_search($day->day, $pl);
	            	} 
            	?>
				<br>
				{{ucfirst($day_pl).':'}} @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#dayModal{{$day->id}}">Edytuj</a>]@endif
				<br>
				{{$day->hour_start}} - {{$day->hour_end}}
				<br>


				<!-- Modal -->
				<div class="modal fade" id="dayModal{{$day->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Godziny otwarcia w {{$day_pl}}</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				        <form method="POST" action="/solo_edit_day_time">
					      	{{ csrf_field() }}
					      		<input type='hidden' name='name' value={{$name}}>
				      			<input type='hidden' name='day' value={{$day->day}}>
					      		<div class="form-inline col-md-* " style="margin-bottom: 2rem">
									<label class='form-control-label' for="day_hour_start">Od godziny: </label>
									<input type="text" name="day_hour_start" class="form-control" id="day_hour_start">
								</div>
								<div class="form-inline col-md-* " style="margin-bottom: 2rem">
									<label class='form-control-label' for="day_hour_end">Do godziny: </label>
									<input type="text" name="day_hour_end" class="form-control" id="day_hour_end">
								</div>
								<input hidden type="text" name="id" class="form-control" id="id" value="{{$day->id}}">
				 				<div class="form-group  col-md-6" >
									<label for="choose_day"><b>Wybierz dzień:</b></label>
									<select class="form-control" name="choose_day" id="choose_day">
										<option value='everyday'>Codziennie</option>
										<option value='monday'>Poniedziałek</option>
										<option value='tuesday'>Wtorek</option>
										<option value='wednesday'>Środa</option>
										<option value='thursday'>Czwartek</option>
										<option value='friday'>Piątek</option>
										<option value='saturday'>Sobota</option>
										<option value='sunday'>Niedziela</option>
									</select>
								</div>
				      	</div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					        <button type="submit" class="btn btn-primary">Zapisz</button>
					      </div>
				      	</form>
				      	<form method="POST" action="/solo_remove_day">
				      		{{ csrf_field() }}
				      		<input type='hidden' name='name' value={{$name}}>
				      		<input type='hidden' name='day' value={{$day->day}}>
				      		<input hidden type="text" name="id" class="form-control" id="id" value="{{$day->id}}">
							<button type="submit" class="btn btn-danger">Usuń</button>
						</form>
						
				    </div>
				  </div>
				</div>
				<!-- Modal -->

			@endforeach
		@endif


		<div class="form-inline" style="margin-top:3rem;margin-bottom:1rem">
			@if(Auth::user()->role=='admin')<button type='submit' class='btn btn-md btn-primary' data-toggle="modal" data-target="#add_day" style="margin-right:1rem ">Dodaj dzień</button>@endif
		</div>

		<div class="form-inline">
			<form method='POST' action="/object/solo/remove" style="margin-right:1rem ">
			{{ csrf_field() }}
			
			<input type='hidden' name='name' value={{$name}}>
			@if(Auth::user()->role=='admin')<button type='submit' class='btn btn-md btn-danger'>Usuń obiekt</button>@endif
			</form>

			<button type='submit' class='btn btn-md btn-warning' data-toggle="modal" data-target="#CloseObject">Zamknij obiekt</button>
		</div>
	</div>


<!-- Modal -->
<div class="modal fade" id="ShowEvent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Akcje administracyjne</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	Przejdź do akcji użytkownika:<p>
      		<?php $i=0;?>
	        @foreach($users_event as $user)
	        	<a href="/users_list/inactive_reservations/solo/{{$name}}/{{$user->user_id}}">{{$user->email}}</a><br>
	        	@foreach($user_hours_event as $user_hour)
	        		Termin nr. <?php echo $i; ?>: <span class="special_font">{{$user_hour->reservation_start}},</span><br>
	        		<span class="special_font" style="padding-left:6rem">{{$user_hour->reservation_end}},</span><br>
	        		<?php $i++; ?>
	        	@endforeach
	        @endforeach
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>

      </div>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="ShowReservationsInactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Rezerwacje użytkowników</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	Przejdź do rezerwacji użytkownika:<p>
      		<?php $i=0;?>
	        @foreach($users_inactive as $user)
	        	<a href="/users_list/inactive_reservations/solo/{{$name}}/{{$user->user_id}}">{{$user->email}}</a><br>
	        	@foreach($user_hours_inactive as $user_hour)
	        		Termin nr. <?php echo $i; ?>: <span class="special_font">{{$user_hour->reservation_start}},</span><br>
	        		<span class="special_font" style="padding-left:6rem">{{$user_hour->reservation_end}},</span><br>
	        		<?php $i++; ?>
	        	@endforeach
	        @endforeach
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>

      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="ShowReservationsActive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Rezerwacje użytkowników</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	Przejdź do rezerwacji użytkownika:<p>
      		<?php $i=0;?>
	        @foreach($users_active as $user)
	        	<a href="/users_list/active_reservations/solo/{{$name}}/{{$user->user_id}}">{{$user->email}}</a><br>
	        	@foreach($user_hours_active as $user_hour)

	        		Termin nr. <?php echo $i; ?>: <span class="special_font">{{$user_hour->reservation_start}},</span><br>
	        		<span class="special_font" style="padding-left:6rem">{{$user_hour->reservation_end}},</span><br>
	        		<?php $i++; ?>
	        	@endforeach
	        @endforeach
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>

      </div>
    </div>
  </div>
</div>



<!-- Modal -->

<div class="modal fade" id="add_day" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Dodaj otwarcie w dniu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form method="POST" action="/solo_add_day">
		      	{{ csrf_field() }}
		      	<input type='hidden' name='name' value={{$name}}>
	      		<div class="form-inline col-md-* " style="margin-bottom: 2rem">
					<label class='form-control-label' for="day_hour_start">Od godziny: </label>
					<input type="text" name="day_hour_start" class="form-control" id="day_hour_start">
				</div>
				<div class="form-inline col-md-* " style="margin-bottom: 2rem">
					<label class='form-control-label' for="day_hour_end">Do godziny: </label>
					<input type="text" name="day_hour_end" class="form-control" id="day_hour_end">
				</div>
				<div class="col-md-12">
	  				<input type="checkbox" name="day[0]" class="day" value="monday"> Poniedziałek
	  				<input type="checkbox" name="day[1]" class="day" value="tuesday "> Wtorek
	  				<input type="checkbox" name="day[2]" class="day" value="wednesday "> Środa <br>
	  				<input type="checkbox" name="day[3]" class="day" value="thursday "> Czwartek
	  				<input type="checkbox" name="day[4]" class="day" value="friday "> Piątek
	  				<input type="checkbox" name="day[5]" class="day" value="saturday "> Sobota
	  				<input type="checkbox" name="day[6]" class="day" value="sunday "> Niedziela<br><br>
  				</div>
  					<input type="checkbox" name="day[7]" class="visible" value="everyday" id="everyday"> <span class='visible'>Codziennie</span><br><br>
		</div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
	        <button type="submit" class="btn btn-primary">Dodaj</button>
	      </div>
      	</form>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="CloseObject" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Zamknięcie obiektu {{$name}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="/close_solo_object">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      				<div class="form-group">
	      					<div class="form-inline col-md-* " style="margin-bottom: 0rem">
								<label class='form-control-label' for="date_start">Od dnia: </label>
								<input type="text" name="date_start" class="form-control datepicker" readonly='true' id="dateajax_start">
							</div>
							<div class="form-inline col-md-* " style="margin-bottom: 2rem">
								<label class='form-control-label' for="hour_start">Od godziny: </label>
								<input type="text" name="hour_start" class="form-control" id="hour_start">
							</div>
							<div class="form-inline col-md-* " style="margin-bottom: 0rem">
								<label class='form-control-label' for="date_end">Do dnia: </label>
								<input type="text" name="date_end" class="form-control endDatePicker" readonly='true' id="dateajax_end">
							</div>
							<div class="form-inline col-md-* " style="margin-bottom: 2rem">
								<label class='form-control-label' for="hour_end">Do godziny: </label>
								<input type="text" name="hour_end" class="form-control "  id="hour_end">
							</div>
	      				<label for="close_reason">Powód zamknięcia: </label>
	      				<input type="text" name="close_reason">
	      			</div>
	      		</div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
	        <button type="submit" class="btn btn-primary">Zapisz</button>
	      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->

<div class="modal fade" id="breakModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Czas przerwy</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="/solo_edit_break_time">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      			<div class="form-group">
	      				<label for="break_time">Czas przerwy</label>
	      				<input type="text" name="break_time" pattern="\d*">
	      			</div>
	      		</div>
	      	</div>
      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Zapisz</button>
	      </div>
      	</form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="cost" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edycja kosztu za jedno wejście</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="/solo_edit_cost">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      			<div class="form-group">
	      				<label for="cost">Nowy koszt: </label>
	      				<input type="text" name="cost">
	      			</div>
	      		</div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
	        <button type="submit" class="btn btn-primary">Zapisz</button>
	      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="maxGuests" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edycja maksymalnej liczby gości</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="/solo_edit_maxguests">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      			<div class="form-group">
	      				<label for="maxGuests">Maksymalna liczba gości: </label>
	      				<input type="text" name="maxGuests">
	      			</div>
	      		</div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
	        <button type="submit" class="btn btn-primary">Zapisz</button>
	      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="time" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edycja czasu dla jednego wejścia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="/solo_edit_time">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      			<div class="form-group">
	      				<label for="time">Czas: </label>
	      				<input type="text" name="time">
	      			</div>
	      		</div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
	        <button type="submit" class="btn btn-primary">Zapisz</button>
	      </div>
      </form>
    </div>
  </div>
</div>




<script src="/js/date_picker.js"></script>
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

});
</script>
@endsection



