@extends('layout.master')
@include('layout.errors')
@section('content')
@if (session()->has('information'))
      <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif

	<div class="col-md-6 center" style='padding-bottom: 1rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;' >
		<h3 style="text-align:center; padding: 1rem; margin-bottom:2rem; border-bottom: 2px solid #e6e6e6";>Informacje o obiekcie</h3>
		<div class="form-group">
			Ilość nieaktywnych rezerwacji  [<a href='#' data-toggle="modal" data-target="#ShowReservationsInactive">Użytkownicy</a>]: <span class='special_font'>{{$reserv_amount_inactive}}</span>
		</div>
		<div class="form-group">
			Ilość aktywnych rezerwacji  [<a href='#' data-toggle="modal" data-target="#ShowReservationsActive">Użytkownicy</a>]: <span class='special_font'>{{$reserv_amount_active}}</span>
		</div>
		<div class="form-group">
			Akcje administracyjne  [<a href='#' data-toggle="modal" data-target="#ShowEvent">Zarząd</a>]: <span class='special_font'>{{$reserv_amount_event}}</span>
		</div>
	</div> 

	<div class="col-md-10 center" >
		<h3 style="text-align:center; padding: 3rem 0rem 1rem; margin:1rem 2rem; border-bottom: 2px solid #e6e6e6;border-top: 2px solid #e6e6e6">@if(Auth::user()->role=='admin')<button class='btn btn-sm btn-primary' style="margin-left:-4rem;" onclick="location.href='{{url('new_room/')}}'" >Dodaj nowy </button>@endif Lista pokojów</h3>
		<table class="table table-hover">
				<thead >
					<tr>
						<th >Numer pokoju</th>
						<th>Maksymalna<br>ilość gości</th>
						<th>Cena za dobę</th>
						<th ">Opis</th>
						<th >Opcje</th>
					</tr>
				</thead>
				<tbody>
					
					@foreach($object_rooms as $object_room)

						<tr>
							<th scope="row">{{$object_room->room_number}}</th>
							<td>{{$object_room->max_guests}}</td>
							<td >{{$object_room->cost_night}} zł</td>
							<td class="w-50">{{$object_room->description}}</td>
							<td>
								@if(Auth::user()->role=='admin')
									<button type="button" class="btn btn-light btn-sm edit_user" title="Edycja" data-toggle="modal" data-target="#EditModal{{$object_room->room_number}}" value=><i class="fa fa-pencil-square-o" aria-hidden="true" {{-- onclick="location.href='{{url('edit/'.$user->user_id)}}'" --}}></i></button>
									<button type="button" class="btn btn-light btn-sm" title="Usuń" data-toggle="modal" data-target="#RemoveModal{{$object_room->room_number}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
								@endif
								<button type="button" class="btn btn-light btn-sm" title="Zamknij" data-toggle="modal" data-target="#CloseModal{{$object_room->room_number}}"><i class="fa fa-window-close-o" aria-hidden="true"></i></button>
							</td>
							
						</tr>


                        <!-- Modal -->
                     {{-- MODALE --}}
                     
						<div class="modal fade" id="CloseModal{{$object_room->room_number}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Zamknięcie pokoju {{$object_room->room_number}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						      	<form method="POST" action="/close_room">
							      	{{ csrf_field() }}
							      	<input type='hidden' name='room_number' value={{$object_room->room_number}}>
							      	<div class="row">
							      		<div class="col-md-6">
							      				<div class="form-group">
							      					<div class="form-inline col-md-* " style="margin-bottom: 0rem">
														<label class='form-control-label' for="date_start">Od kiedy: </label>
														<input type="text" name="date_start" class="form-control datepicker" readonly='true' >
													</div>
													<div class="form-inline col-md-* " style="margin-bottom: 0rem">
														<label class='form-control-label' for="date_end">Do kiedy: </label>
														<input type="text" name="date_end" class="form-control endDatePicker" readonly='true' >
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
						<div class="modal fade" id="EditModal{{$object_room->room_number}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Edycja pokoju {{$object_room->room_number}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						      	<form method="POST" action="/hotel_edit_room">
							      	{{ csrf_field() }}
							      	<input type='hidden' name='number' value={{$object_room->room_number}}>
							      	<div class="row">
							      		<div class="col-md-6">
							      			<div class="form-group">
							      				<label for="max_guests">Maksymalna liczba gości: </label>
							      				<input type="text" name="max_guests" pattern="\d*" value="{{$object_room->max_guests}}">
							      			</div>
							      			<div class="form-group">
							      				<label for="cost">Cena za dobę: </label>
							      				<input type="text" name="cost" pattern="\d*" value="{{$object_room->cost_night}}">
							      			</div>
							      			<div class="form-group">
							      				<label for="description">Opis: </label>
							      				<textarea rows="4" cols="50" name="description" value=" ">{{$object_room->description}} </textarea>
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
						<div class="modal fade" id="RemoveModal{{$object_room->room_number}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Usunięcie pokoju {{$object_room->room_number}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						      	<form method="POST" action="/hotel_remove_room">
						      		
							      	{{ csrf_field() }}
							      	<input type='hidden' name='number' value={{$object_room->room_number}}>
							      	<div class="row">
							      		<div class="col-md-12">
							      			Czy na pewno chcesz usunąć ten pokój?
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
	        	<a href="/users_list/inactive_reservations/hotel/{{$user->user_id}}">{{$user->email}}</a><br>
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
	        	<a href="/users_list/inactive_reservations/hotel/{{$user->user_id}}">{{$user->email}}</a><br>
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
	        	<a href="/users_list/active_reservations/hotel/{{$user->user_id}}">{{$user->email}}</a><br>
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
<script src="/js/date_picker.js"></script>

@endsection









