@extends('layout.master')
@include('layout.errors')
@section('content')
	@if (session()->has('information'))
	  <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
	@endif

	@if($reservations->count()==0)
		<div class="col-md-4 center" style="margin-top: 3rem">
		<h2 >Brak rezerwacji</h2>
		</div>
	@else
		<div class="col-md-8 offset-2" style="margin-top: 3rem">
			<h2>Moje Rezerwacje</h2>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Numer Rezerwacji</th>
						<th>Do zapłaty</th>
						<th>Opcje</th>
					</tr>
				</thead>
				<tbody class='ajax'>
					
					@foreach($reservations as $reservation)
						<tr>
							<th>{{$reservation->reservation_id}}</td>
							<td>{{$reservation->total_cost}} zl</td>
							<td>	
								<div class="form-inline">
									<button type="button" class="btn btn-light btn-sm .active_reservations" title="Aktywne Rezerwacje" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-address-book" ></i></button>
									{{-- DROPDOWN DLA BUTTONA --}}
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">



										{{-- POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}
										@foreach($hotels as $hotel)
											@if($hotel->reservation_id==$reservation->reservation_id)
												<a class="dropdown-item" href="{{url('/my_reservations/hotel/'.$reservation->reservation_id)}}">Hotel</a>
												<?php break; ?>
											@endif
										@endforeach
										 {{-- POKAZUJE TYLKO GDY JEST REZERWACJA HOTELU --}}


										{{-- POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}
										<?php $j=sizeof($group); ?>
										@for($i=0;$i<$j;$i++)
											<?php $cond=false; ?>
											   
										    @foreach($result_group[$group[$i]->object_name] as $res)
										    	@if($res->reservation_id==$reservation->reservation_id)
										    		<?php $cond=true; break;?>
										    	@endif
										    @endforeach

									    	@if($cond==true)
												<a class="dropdown-item" href="{{url('/my_reservations/group/'.$group[$i]->object_name).'/'.$reservation->reservation_id}}">{{str_replace('_', ' ', $group[$i]->object_name)}}</a>   
											@endif
													
										@endfor
										{{-- POKAZUJE TYLKO GDY JEST REZERWACJA GRUPOWA --}}




										{{-- POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}
									    <?php $j=sizeof($solo); ?>
									    @for($i=0;$i<$j;$i++)
									    	<?php $cond=false; ?>
									   
										    @foreach($result[$solo[$i]->object_name] as $res)
										    	@if($res->reservation_id==$reservation->reservation_id)
										    		<?php $cond=true; break;?>
										    	@endif
										    @endforeach

									    	@if($cond==true)
												<a class="dropdown-item" href="{{url('/my_reservations/solo/'.$solo[$i]->object_name.'/'.$reservation->reservation_id)}}">{{str_replace('_', ' ', $solo[$i]->object_name)}}</a>   
											@endif
											
									    @endfor
											    {{-- POKAZUJE TYLKO GDY JEST REZERWACJA INDYWIDUALNA --}}



									</div>
									{{-- KONIEC DROPDOWN --}}

									<button type="button" class="btn btn-light btn-sm" title="Nieaktywne Rezerwacje"><i class="fa fa-address-book-o" aria-hidden="true"></i></button>
									
									<form method="POST" action="/my_reservations/remove">
									{{ csrf_field() }}
										<input type="hidden" name="reservation_id" id="reservation_id" value="{{$reservation->reservation_id}}">
										@if($reservation->status==0)
											<button type="submit" class="btn btn-light btn-sm" title="Usuń" ><i class="fa fa-trash" aria-hidden="true"></i></button> 
										@endif()
									</form>
								</div>
							</td>
						</tr>

					@endforeach
				</tbody>
			</table>
		</div>
	@endif
@endsection
