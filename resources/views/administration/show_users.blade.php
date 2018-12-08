@extends('layout.master')
@include('layout.errors')
@section('content')
	@if (session()->has('information'))
	  <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
	@endif
	<div class="col-md-10 offset-1" style="margin-top: 3rem">
		<h3>Użytkownicy aplikacji</h3>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Rola</th>
					<th>E-mail</th>
					<th>Nazwisko</th>
					<th>Do zapłaty</th>
					<th>Opcje</th>
				</tr>
			</thead>
			<tbody class='ajax'>
				<?php $i=0;?>
				@foreach($users as $user)
				
					<tr>
						<th scope="row">{{$user->user_id}}</th>
						<td>{{$user->role}}</td>
						<td>{{$user->email}}</td>
						<td>{{$user->last_name}}</td>
						<td>
							<?php $total_cost=0; ?>
							@foreach($reservations as $reservation)
								@if($reservation->user_id==$user->user_id)
									<?php $total_cost+=$reservation->total_cost;?>
								@endif
							@endforeach
							{{$total_cost}}
						</td>

						<td>
							@if($user->role=='admin' || $user->role=='organizator')
								<button type="button" class="btn btn-light btn-sm active_reservations" title="Aktywne Działania" id="dropdownMenu" data-taget='dzialania' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-address-card" aria-hidden="true"></i></button>
								{{-- DROPDOWN DLA BUTTONA --}}
								<div class="dropdown-menu dzialania" aria-labelledby="dropdownMenuButton">



									{{-- POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}
									@foreach($hotels as $hotel)
										@if($hotel->user_id==$user->user_id)
											<a class="dropdown-item" href="{{url('/users_list/inactive_reservations/hotel/'.$user->user_id)}}">Hotel</a>
											<?php break; ?>
										@endif
									@endforeach
									 {{-- POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}


									{{-- POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}
									<?php $j=sizeof($group); ?>
									@for($i=0;$i<$j;$i++)
										<?php $cond=false; ?>
										    {{-- JAKIS WARUNEK??? --}}
									    @foreach($result_group[$group[$i]->object_name] as $res)
									    	@if($res->user_id==$user->user_id)
									    		<?php $cond=true; break;?>
									    	@endif
									    @endforeach

								    	@if($cond==true)
											<a class="dropdown-item" href="{{url('/users_list/inactive_reservations/group/'.$group[$i]->object_name.'/'.$user->user_id)}}">{{str_replace('_', ' ', $group[$i]->object_name)}}</a>   
										@endif
												
									@endfor
									{{-- POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}




										    {{-- POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}
								    <?php $j=sizeof($solo); ?>
								    @for($i=0;$i<$j;$i++)
								    	<?php $cond=false; ?>
								    {{-- JAKIS WARUNEK??? --}}
									    @foreach($result[$solo[$i]->object_name] as $res)
									    	@if($res->user_id==$user->user_id)
									    		<?php $cond=true; break;?>
									    	@endif
									    @endforeach

								    	@if($cond==true)
											<a class="dropdown-item" href="{{url('/users_list/inactive_reservations/solo/'.$solo[$i]->object_name.'/'.$user->user_id)}}">{{str_replace('_', ' ', $solo[$i]->object_name)}}</a>   
										@endif
										
								    @endfor
										    {{-- POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}



								</div>
								{{-- KONIEC DROPDOWN --}}


							@else	

								<div class="btn-group">
									<button type="button" class="btn btn-light btn-sm inactive" title="Nieaktywne Rezerwacje" id="dropdownMenuButtonInactive" data-taget='nieaktywny'  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-address-book-o" ></i></button>
									{{-- INACTIVE DROPDOWN DLA BUTTONA --}}
									<div class="dropdown-menu nieaktywny" aria-labelledby="dropdownMenuButtonInactive">



										{{-- INACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}
										@foreach($hotels as $hotel)
											@if(($hotel->user_id==$user->user_id) && ($hotel->status==0))
												<a class="dropdown-item" href="{{url('/users_list/inactive_reservations/hotel/'.$user->user_id)}}">Hotel</a>
												<?php break; ?>
											@endif
										@endforeach
										 {{-- INACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}


										{{-- INACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}
										<?php $j=sizeof($group); ?>
										@for($i=0;$i<$j;$i++)
											<?php $cond=false; ?>
											    {{-- JAKIS WARUNEK??? --}}
										    @foreach($result_group[$group[$i]->object_name] as $res)
										    	@if(($res->user_id==$user->user_id) && ($res->status==0))
										    		<?php $cond=true;?>
										    	@else
										    		<?php $cond=false;?>
										    	@endif
										    @endforeach

									    	@if($cond==true)
												<a class="dropdown-item" href="{{url('/users_list/inactive_reservations/group/'.$group[$i]->object_name.'/'.$user->user_id)}}">{{str_replace('_', ' ', $group[$i]->object_name)}}</a>   
											@endif
													
										@endfor
										{{-- INACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}




											    {{-- INACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}
									    <?php $j=sizeof($solo); ?>
									    @for($i=0;$i<$j;$i++)
									    	<?php $cond=false; ?>
									    {{-- JAKIS WARUNEK??? --}}
										    @foreach($result[$solo[$i]->object_name] as $res)
										    	@if(($res->user_id==$user->user_id) && ($res->status==0))
										    		<?php $cond=true;?>
										    	@else
										    		<?php $cond=false;?>
										    	@endif
										    @endforeach

									    	@if($cond==true)
												<a class="dropdown-item" href="{{url('/users_list/inactive_reservations/solo/'.$solo[$i]->object_name.'/'.$user->user_id)}}">{{str_replace('_', ' ', $solo[$i]->object_name)}}</a>   
											@endif
											
									    @endfor
											    {{-- INACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}



									</div>
									{{-- KONIEC DROPDOWN --}}

								</div>
								
								<div class="btn-group">
									<button type="button" class="btn btn-light btn-sm" title="Aktywne Rezerwacje" id="dropdownMenuButtonActive" data-target='aktywny' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-address-book" aria-hidden="true"></i></button>

									{{-- ACTIVE DROPDOWN DLA BUTTONA --}}
									<div class="dropdown-menu aktywny" aria-labelledby="dropdownMenuButtonActive">



										{{-- ACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}
										@foreach($hotels as $hotel)
											@if(($hotel->user_id==$user->user_id) && ($hotel->status==1))
												<a class="dropdown-item" href="{{url('/users_list/active_reservations/hotel/'.$user->user_id)}}">Hotel</a>
												<?php break; ?>
											@endif
										@endforeach
										 {{-- ACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}


										{{-- ACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}
										<?php $j=sizeof($group); ?>
										@for($i=0;$i<$j;$i++)
											<?php $cond=false; ?>
											    {{-- JAKIS WARUNEK??? --}}
										    @foreach($result_group[$group[$i]->object_name] as $res)
										    	@if(($res->user_id==$user->user_id) && ($res->status==1))
										    		<?php $cond=true;break;?>
										    	@else
										    		<?php $cond=false; ?>
										    	@endif
										    @endforeach

									    	@if($cond==true)
												<a class="dropdown-item" href="{{url('/users_list/active_reservations/group/'.$group[$i]->object_name.'/'.$user->user_id)}}">{{str_replace('_', ' ', $group[$i]->object_name)}}</a>   
											@endif
													
										@endfor
										{{-- ACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}




											    {{-- ACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}
									    <?php $j=sizeof($solo); ?>
									    @for($i=0;$i<$j;$i++)
									    	<?php $cond=false; ?>
									    {{-- JAKIS WARUNEK??? --}}
										    @foreach($result[$solo[$i]->object_name] as $res)
										    	@if(($res->user_id==$user->user_id) && ($res->status==1))
										    		<?php $cond=true;break;?>
										    	@else
										    		<?php $cond=false;?>
										    	@endif
										    @endforeach

									    	@if($cond==true)
												<a class="dropdown-item" href="{{url('/users_list/active_reservations/solo/'.$solo[$i]->object_name.'/'.$user->user_id)}}">{{str_replace('_', ' ', $solo[$i]->object_name)}}</a>   
											@endif
											
									    @endfor
											    {{-- ACTIVE POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}



									</div>
									{{-- KONIEC DROPDOWN --}}


								</div>

							@endif
								<button type="button" class="btn btn-light btn-sm edit_user" title="Edycja" data-toggle="modal" data-target="#EditUserModal{{$user->user_id}}" value={{$user->user_id}}><i class="fa fa-pencil-square-o" aria-hidden="true" {{-- onclick="location.href='{{url('edit/'.$user->user_id)}}'" --}}></i></button>
								@if(Auth::user()->user_id!=$user->user_id && (Auth::user()->role!='organizator' && $user->role!='Administrator'))
									<button type="button" class="btn btn-light btn-sm" title="Usuń" data-toggle="modal" data-target="#UserDeleteModal{{$user->user_id}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
								@endif
						</td>
					</tr>


					{{-- MODAL --}}{{-- MODAL --}}{{-- MODAL --}}

					<div class="modal fade" id="EditUserModal{{$user->user_id}}" tabindex="-1" role="dialog" aria-labelledby="EditUserModalLabel" aria-hidden="true">		
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
			    						<h5 class="modal-title" id="EditUserModalLabel">Edycja Użytkownika</h5>
			    							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			    								<span aria-hidden="true">&times;</span>
			    						</button>
			    					</div>
			    					<div class="modal-body">
			    						<form method='POST' action='/users_list/edit/{{$user->user_id}}'>
			    				
			    							{{ csrf_field() }}

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="email">Email:</label>
														<input type="email" class="form-control" name="email" id="email" placeholder="Example@gmail.com" value={{$user->email }}>
													</div>

												<div class="form-group">
													<label for="password">Hasło:</label>
													<input type="password" class="form-control" name="password" id="password" placeholder="Minimum 6 znaków" >
												</div>

												<div class="form-group">
													<label for="password_confirmation">Potwierdzenie Hasła:</label>
													<input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Potwierdź hasło">
												</div>
											</div>
											<div class="col-md-6">

												<div class="form-group">
													<label for="first_name">Imię:</label>
													<input type="text" class="form-control" name="first_name" id="first_name" value={{ $user->first_name }}>
												</div>
												<div class="form-group">
													<label for="last_name">Nazwisko:</label>
													<input type="text" class="form-control" name="last_name" id="last_name" value={{ $user->last_name }}>
												</div>
												<div class="form-group">
													<label for="phone">Telefon:</label>
													<input type="text" class="form-control" name="phone" id="phone" value={{ $user->phone }}>
												</div>
												<div class="form-group">
													<label for="city">Miasto:</label>
													<input type="text" class="form-control" name="city" id="city" value={{ $user->city }}>
												</div>
											</div>
										</div>
									<div class="form-group" style="margin-left: 40%;margin-top: 1rem" >
											<button type="submit" class="btn btn-primary">Zapisz Zmiany!</button>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal USUWANIE-->
					<div class="modal fade" id="UserDeleteModal{{$user->user_id}}" tabindex="-1" role="dialog" aria-labelledby="ActiveReservationsModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="ActiveReservationsModalLabel">Usuwanie użytkownika</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					      	Czy na pewno chcesz usunąć użytkownika {{$user->last_name}} o mailu : {{$user->email}}
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-secondary" data-dismiss="modal">Wyjdź</button>
					        <button type="button" class="btn btn-primary" onclick="location.href='{{url('/users_list/remove/'.$user->user_id)}}'">Usuń</button>
					      </div>
					    </div>
					  </div>
					</div>
				<!-- Modal -->
				@endforeach
			</tbody>
		</table>
	</div>
	<script>
	$('.edit_user').on('click', function (event) {
		event.preventDefault();
		var user_id=$('.edit_user').val();
		$.ajax({
	        type: "GET",
	        url: '/users_list/edit/'+user_id,
	        dataType: 'json',
	        success:function(data){
	         
	            $('.ajax').append(data.body);
	        }
   		});
	})
	</script>
@endsection
