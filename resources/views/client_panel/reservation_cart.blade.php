@extends('layout.master')
@include('layout.errors')
@section('content')
@if (session()->has('information'))
      <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif
<div class="col-md-12 ">
<?php $total_cost=0;?>
	@if($solo_count>0)
		<table class="table table-hover" style="margin-top: 1rem;margin-bottom: 3rem;font-size:14px;">
				<?php $i=0;?>
				<h5 style="margin-top: 3rem;">Oczekujące rezerwacje Indywidualne</h5>
				<thead>
					<tr>
						
						<th>Termin</th>
						<th>Liczba Gości</th>
						<th>Obiekt</th>
						<th>Koszt za wejście na osobę</th>
						<th>Opcje </th> 
					</tr>
				</thead>
				@foreach($solo_carts as $solo_cart)
					
					<tbody>
						<tr>
							
							<td>{{$solo_cart->reservation_start}}</td>
							<td>{{$solo_cart->guests_amount}}</td>
							<td>{{$solo_cart->object_type}}</td>
							<td>{{$solo_cart->cost_hour}}zl</td>
							<td>						
								 <button type="button" class="btn btn-light btn-sm" title="Usuń"   onclick="location.href='{{url('/reservations_in_cart/remove/'.$solo_cart->user_id.'/'.$solo_cart->id.'/solocarts')}}'"  ><i class="fa fa-trash" aria-hidden="true"></i></button> 
							</td>
						</tr>
					</tbody>

				<?php $total_cost+=($solo_cart->guests_amount*$solo_cart->cost_hour); ?>
				@endforeach
			
		</table>
	@endif

		@if($group_count>0)
		<table class="table table-hover" style="margin-bottom: 3rem;font-size:14px;">
				<?php $i=0;?>
				<h5>Oczekujące rezerwacje Grupowe</h5>
				<thead>
					<tr>
						
						<th>Termin</th>
						<th>Numer Boiska</th>
						<th>Obiekt</th>
						<th>Koszt</th>
						<th>Opcje </th> 
					</tr>
				</thead>
				@foreach($group_carts as $group_cart)
					
					<tbody>
						<tr>
							
							<td>{{$group_cart->reservation_start}}</td>
							<td>{{$group_cart->field_number}}</td>
							<td>{{rtrim(str_replace('_', ' ', $group_cart->object_name),"s")}}</td>
							<td>{{$group_cart->cost}}zl</td>
							<td>						
								<button type="button" class="btn btn-light btn-sm" title="Usuń"   onclick="location.href='{{url('/reservations_in_cart/remove/'.$group_cart->user_id.'/'.$group_cart->id.'/groupcarts')}}'"  ><i class="fa fa-trash" aria-hidden="true"></i></button>  
							</td>
						</tr>
					</tbody>

					<?php $total_cost+=$group_cart->cost; ?>
				@endforeach
			
		</table>
	@endif

	@if($hotel_count>0)
		<table class="table table-hover" style="margin-bottom: 3rem;font-size:14px;">
				<?php $i=0;?>
				<h5>Oczekujące rezerwacje Hotelu</h5>
				<thead>
					<tr>
						
						<th>Od kiedy</th>
						<th>Do kiedy</th>
						<th>Liczba gości</th>
						<th>Numer pokoju</th>
						<th>Koszt całkowity</th>
						<th>Opcje </th> 
					</tr>
				</thead>
				@foreach($hotel_carts as $hotel_cart)
					
					<tbody>
						<tr>
							
							<td>{{$hotel_cart->reservation_start}}</td>
							<td>{{$hotel_cart->reservation_end}}</td>
							<td>{{$hotel_cart->guests_amount}}</td>
							<td>{{$hotel_cart->room_number}}</td>
							<td>{{$hotel_cart->cost}}zl</td>
							<td>						
								<button type="button" class="btn btn-light btn-sm" title="Usuń"   onclick="location.href='{{url('/reservations_in_cart/remove/'.$hotel_cart->user_id.'/'.$hotel_cart->id.'/hotelcarts')}}'"  ><i class="fa fa-trash" aria-hidden="true"></i></button> 
							</td>
						</tr>
					</tbody>
					<?php $total_cost+=$hotel_cart->cost; ?>
				@endforeach
		</table>
	@endif

	<span style='font-weight: 900;font-size:1.5rem;'>Do zapłaty: </span><span style='color:#009933;font-weight: 900;font-size:1.5rem'> <?php echo $total_cost; ?></span>
	@if (($solo_count>0) || ($group_count>0) || ($hotel_count>0))
		<form method="POST" action="/reserve_cart" >
			{{ csrf_field() }}
			@if(Auth::user()->role=='admin')
				<div class="form-group col-md-4" style="margin-top: 3rem; margin-bottom: 3rem" >
					<label for="who">Dla kogo?:</label>
					<select class="form-control" name="who" id="who">
						@foreach($all_users as $user)
							@if($user->role=='klient')
								<option value="{{$user->user_id}}">{{$user->email}}</option>
							@endif
						@endforeach
					</select>
				</div>
			
			@endif
			<div class="col">
				<button type="submit" class="btn btn-primary btn-lg" title="Zatwierdź"  >Zatwierdź Rezerwację</button> 
				<button type="button" class="btn btn-secondary btn-lg" title="Anuluj"  onclick="location.href='{{url('reservations_in_cart/remove_cart/'.$my_id)}}'">Anuluj</button>
			</div>
		</form>
	@endif

</div>


@endsection