@extends('layout.master')
@include('layout.errors')
@section('content')
@if (session()->has('information'))
      <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif
<div class="col-md-10 offset-1 " style="margin-top: 3rem">
	<h2>Moje rezerwacje {{str_replace('_', ' ', $name)}}</h2>
	<table class="table table-hover">
		<thead>
				<tr>
					<th>Początek rezerwacji</th>
					<th>Koniec rezerwacji</th>
					<th>Ilość osób</th>
					<th>Koszt<br>za rezerwację</th>
					<th>Koszt<br>na osobę</th>
					{{-- <th>Opcje </th>  --}}
				</tr>
		</thead>


		@foreach($active_reservations as $active_reservation)
			
			<tbody class="{{$active_reservation->reservation_id}}">
				<tr>
					<td><b>{{$active_reservation->reservation_start}}</b></td>
					<td><b>{{$active_reservation->reservation_end}}</b></td> 
					<td>{{$active_reservation->guests_amount}}</td>
					<td><?php echo ($active_reservation->cost_hour)*($active_reservation->guests_amount).'zl' ?></td>
					<td>{{$active_reservation->cost_hour}}</td>
					{{-- <form method="POST" action="my_reservations/remove/solo">
					{{ csrf_field() }}
						<input type="hidden" name="reservation_id" id="reservation_id" value="{{$active_reservation->reservation_start}}">
						<input type="hidden" name="name" id="name" value="{{$name}}">
						<input type="hidden" name="reservation_id" id="reservation_id" value="{{$active_reservation->reservation_id}}">
					<td> <button type="submit" class="btn btn-light btn-sm" title="Usuń" ><i class="fa fa-trash" aria-hidden="true"></i></button> </td>
					</form> --}}
				</tr>
			</tbody>
		
		@endforeach
	</table>
</div>


@endsection