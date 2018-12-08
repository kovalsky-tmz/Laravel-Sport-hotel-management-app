@extends('layout.master')
@include('layout.errors')
@section('content')
@if (session()->has('information'))
      <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif

	<div class="col-md-6 center" style='padding-bottom: 1rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;'>
		<h3 style="text-align:center; padding: 1rem; margin-bottom:2rem; border-bottom: 2px solid #e6e6e6";>Informacje o obiekcie</h3>
		<div class="form-group">
			Nazwa obiektu: <span class='special_font'> {{str_replace('_', ' ', $name)}}</span>
		</div>
		<div class="form-group">
			System:<span class='special_font'> Grupowy</span>
		</div>
		<div class="form-group">
			Czas dla jednego wejścia @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#sequenceModal">Edytuj</a>]@endif : <span class='special_font'>{{$object->sequence_time}}</span>
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
		<div class="form-group" style="padding:2rem 0">
			<form method='POST' action="/object/group/remove">
			{{ csrf_field() }}
			<input type='hidden' name='name' value={{$name}}>
			@if(Auth::user()->role=='admin')<button type='submit' class='btn btn-md btn-danger'>Usuń obiekt</button>@endif
			</form>
		</div>

	</div>

	<div class="col-md-12 center" >
		<h3 style="text-align:center; padding: 3rem 0rem 1rem; margin:2rem 2rem; border-bottom: 2px solid #e6e6e6;border-top: 2px solid #e6e6e6">@if(Auth::user()->role=='admin')<button class='btn btn-sm btn-primary' style="margin-left:-4rem;" onclick="location.href='{{url('new_field/'.$name)}}'">Dodaj nowy </button>@endif Lista obiektów</h3>
		<table class="table table-hover">
				<thead>
					<tr>
						<th>Numer objektu</th>
						<th>Typ obiektu</th>
						<th>Cena za wejście</th>
						<th class="w-25">Opis</th>
						<th><a href="#">@if(Auth::user()->role=='admin')<i class="fa fa-plus-circle" aria-hidden="true" data-toggle="modal" data-target="#add_day"></i>@endif</a> Dni otwarcia </th>
						<th>Opcje</th>
					</tr>
				</thead>
				<tbody>
					
					@foreach($object_fields as $object_field)
						<?php $field_number=$object_field->field_number;?>
						<tr>
							<th scope="row">{{$object_field->field_number}}</th>
							<td>{{$object_field->field_type}}</td>
							<td>{{$object_field->cost_per_entrance}} zł</td>
							<td>{{$object_field->description}}</td>
							<td>
								@foreach($days[$field_number] as $day)

									<?php 
										if(array_search($day->day, $pl)==true){
						                	$day_pl=array_search($day->day, $pl);
						            	} 
						            ?>
						            
									{{ucfirst($day_pl).':'}} @if(Auth::user()->role=='admin')[<a href='#' data-toggle="modal" data-target="#dayModal{{$day->id}}">Edytuj</a>]@endif
									<br>
									{{$day->hour_start}} - {{$day->hour_end}}
									<br><br>
									
									<!-- Modal -->
									<div class="modal fade" id="dayModal{{$day->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									  <div class="modal-dialog" role="document">
									    <div class="modal-content">
									      <div class="modal-header">
									        <h5 class="modal-title" id="exampleModalLabel">Godziny otwarcia w {{$day_pl}} - {{$object_field->field_type}}</h5>
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									          <span aria-hidden="true">&times;</span>
									        </button>
									      </div>
									      <div class="modal-body">
									        <form method="POST" action="/group_edit_day_time">
										      	{{ csrf_field() }}
										      		<input type='hidden' name='day' value={{$day->day}}>
										      		<input type='hidden' name='name' value={{$name}}>
										      		<input type='hidden' name='field_number' value={{$field_number}}>
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
									      	<form method="POST" action="/group_remove_day">
									      		{{ csrf_field() }}
									      		<input type='hidden' name='day' value={{$day->day}}>
										      	<input type='hidden' name='name' value={{$name}}>
										      	<input type='hidden' name='field_number' value={{$field_number}}>
									      		<input hidden type="text" name="id" class="form-control" id="id" value="{{$day->id}}">
												<button type="submit" class="btn btn-danger">Usuń</button>
											</form>
											
									    </div>
									  </div>
									</div>
									<!-- Modal -->

								@endforeach
							</td>
							<td>
								<div class="form-inline">
									@if(Auth::user()->role=='admin')
										<button type="button" class="btn btn-light btn-sm edit_user" title="Edycja" data-toggle="modal" data-target="#EditModal{{$object_field->field_number}}"><i class="fa fa-pencil-square-o" aria-hidden="true" {{-- onclick="location.href='{{url('edit/'.$user->user_id)}}'" --}}></i></button>
										<button type="button" class="btn btn-light btn-sm" title="Usuń" data-toggle="modal" data-target="#RemoveModal{{$object_field->field_number}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
									@endif
									<button type="button" class="btn btn-light btn-sm" title="Zamknij" data-toggle="modal" data-target="#CloseField{{$object_field->field_number}}"><i class="fa fa-window-close-o" aria-hidden="true"></i></button>
								</div>
							</td>
							
						</tr>



						<!-- Modal -->
						<div class="modal fade" id="CloseField{{$object_field->field_number}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Zamknięcie objektu {{$object_field->field_type}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						      	<form method="POST" action="/close_group_object">
							      	{{ csrf_field() }}
							      	<input type='hidden' name='name' value={{$name}}>
							      	<input type='hidden' name='field_number' value={{$object_field->field_number}}>
							      	<div class="row">
							      		<div class="col-md-6">
							      				<div class="form-group">
							      					<div class="form-inline col-md-* " style="margin-bottom: 0rem">
														<label class='form-control-label' for="date_start">Od kiedy: </label>
														<input type="text" name="date_start" class="form-control datepicker" readonly='true' >
													</div>
													<div class="form-inline col-md-* " style="margin-bottom: 2rem">
														<label class='form-control-label' for="hour_start">Od godziny: </label>
														<input type="text" name="hour_start" class="form-control" id="hour_start">
													</div>
													<div class="form-inline col-md-* " style="margin-bottom: 0rem">
														<label class='form-control-label' for="date_end">Do kiedy: </label>
														<input type="text" name="date_end" class="form-control endDatePicker" readonly='true' >
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
						<div class="modal fade" id="EditModal{{$object_field->field_number}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Edycja boiska {{$object_field->field_type}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						      	<form method="POST" action="/group_edit_field">
							      	{{ csrf_field() }}
							      	<input type='hidden' name='name' value={{$name}}>
							      	<input type='hidden' name='field_number' value={{$object_field->field_number}}>
							      	<div class="row">
							      		<div class="col-md-6">
							      			<div class="form-group">
							      				<label for="field_type">Nazwa: </label>
							      				<input type="text" name="field_type" value="{{$object_field->field_type}}">
							      			</div>
							      			<div class="form-group">
							      				<label for="cost_per_entrance">Koszt za jedno wejście: </label>
							      				<input type="text" name="cost_per_entrance" value="{{$object_field->cost_per_entrance}}">
							      			</div>
							      			<div class="form-group">
							      				<label for="description">Opis: </label>
							      				<textarea rows="4" cols="50" type="text" name="description" value="">{{$object_field->description}} </textarea>
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
						<div class="modal fade" id="RemoveModal{{$object_field->field_number}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Usunięcie boiska {{$object_field->field_type}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						      	<form method="POST" action="/group_remove_field">
							      	{{ csrf_field() }}
							      	<input type='hidden' name='name' value={{$name}}>
							      	<input type='hidden' name='field_number' value={{$object_field->field_number}}>
							      	<div class="row">
							      		<div class="col-md-12">
							      			Czy na pewno chcesz usunąć ten obiekt?
							      		</div>
							      	</div>
							   </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
							        <button type="submit" class="btn btn-primary">Usuń</button>
							      </div>
						      </form>
						    </div>
						  </div>
						</div>


					@endforeach
				</tbody>
			</table>
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
	        	<a href="/users_list/inactive_reservations/group/{{$name}}/{{$user->user_id}}">{{$user->email}}</a><br>
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
        <h5 class="modal-title" id="exampleModalLabel">Rezerwacje nieaktywne użytkowników</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	Przejdź do rezerwacji użytkownika:<p>
      		<?php $i=0;?>
	        @foreach($users_inactive as $user)
	        	<a href="/users_list/inactive_reservations/group/{{$name}}/{{$user->user_id}}">{{$user->email}}</a><br>
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
        <h5 class="modal-title" id="exampleModalLabel">Rezerwacje aktywne użytkowników</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	Przejdź do rezerwacji użytkownika:<p>
      		<?php $i=0;?>
	        @foreach($users_active as $user)
	        	<a href="/users_list/active_reservations/group/{{$name}}/{{$user->user_id}}">{{$user->email}}</a><br>
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
			<form method="POST" action="/group_add_day">
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
				<div class="form-inline col-md-* " style="margin-bottom: 2rem" >
					<label for="choose_field"><b>Wybierz boisko:</b></label>
					<select class="form-control" name="choose_field" id="choose_field">
						@foreach($object_fields as $object_field)
							<option value="{{$object_field->field_number}}">{{$object_field->field_type}}</option>
						@endforeach
					</select>
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



<!-- Modal -->
<div class="modal fade" id="sequenceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edycja czasu dla jednego wejścia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="/group_edit_time">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      			<div class="form-group">
	      				<label for="time">Czas</label>
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
<!-- Modal -->
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
        <form method="POST" action="/group_edit_break_time">
	      	{{ csrf_field() }}
	      	<input type='hidden' name='name' value={{$name}}>
	      	<div class="row">
	      		<div class="col-md-6">
	      			<div class="form-group">
	      				<label for="break_time">Czas przerwy</label>
	      				<input type="text" name="break_time">
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









