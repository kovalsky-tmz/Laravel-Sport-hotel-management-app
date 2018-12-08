@extends('layout.master')
@include('layout.errors')
@section('content')

	<div class="row" style="margin-top: 3rem">
		
		<div class="col-md-4 browser">
			<div class="col-md-10 options" style="text-align:center;">
				<form method="GET" action="/room_reservation/search">
					{{ csrf_field() }}
					<div class="form-row">
						<div class="form-group col-md-6 " style="margin-bottom: 2rem">
							<label class='form-control-label' for="date_start">Od kiedy: </label>
							<input type="text" name="date_start" class="form-control datepicker" readonly='true' id="dateajax_start" value={{$date_start}}>
						</div>
						<div class="form-group col-md-6 " style="margin-bottom: 0rem">
							<label class='form-control-label' for="date_end">Do kiedy: </label>
							<input type="text" name="date_end" class="form-control endDatePicker" readonly='true' id="dateajax_end" value={{$date_end}}>
						</div>
					</div>
					<div class="form-group col-md-6 center" id="slider-guests_amount">
					</div>
					<p>
						<label for="amount">Liczba gości:</label>
						<input type="text" id="amount" name="slider_guests_amount" value={{$guests_amount}} readonly style="border:0px; color:#f6931f; font-weight:bold;margin-right:-6rem">
					</p>

					<div class="form-group col-md-8 center" id="slider-price_range">
					</div>
					<div class="form-group col-md-12" >
					  <label for="amount_min">Przedział cenowy:</label>
					  <input type="text" id="amount_min" name="amount_min" value={{$cost_night_min}} readonly style="border:0; color:#f6931f; font-weight:bold;margin-right: -11rem">zł
					  <input type="text" id="amount_max" name="amount_max" value={{$cost_night_max}} readonly style="border:0; color:#f6931f; font-weight:bold;margin-right: -11rem">zł
					</div>
					<button type='submit' class='btn btn-primary btn-lg' style="margin-top: 0rem">Szukaj</button>
				</form>
			</div>
		</div>
		<div class="col-md-9 results_browser">
			<table class="table table-hover">
				<thead class="thead-default">
					<tr>
						<th>Numer Pokoju</th>
						<th>Cena za dobę</th>
						<th>Maksimum gości</th>
						<th >Opis</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php $i=0; ?>
					@foreach($result as $result)

						<tr>
							<th scope="row">{{$result->room_number}}</th>
							<td>{{$result->cost_night}} zł</td>
							<td>{{$result->max_guests}} os.</td>
							<td class="w-50">{{$result->description}}</td>

							<td><button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#reserveModal{{$i}}">Zarezerwuj</button></td>
							
						</tr>

						{{-- MODAL DLA REZERWACJI, CZY NA PEWNO CHCE REZERWOWAC --}}
						<div class="modal fade" id="reserveModal{{$i}}" tabindex="-1" role="dialog" aria-labelledby="reserveModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="reserveModalLabel">Potwierdzenie</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										  <span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										Czy na pewno chcesz zarezerwować ten pokój?<p>Pokój: {{$result->room_number}}<br>Od: {{$date_start}}<br>Do: {{$date_end}}<br>Liczba gości: {{$guests_amount}}<br>Cena za dobę: {{$result->cost_night}}</p>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
										<form method="POST" action="/room_reservation/reserve">
											{{ csrf_field() }}
											<input type='hidden' name='date_start' value={{$date_start}}>
											<input type='hidden' name='date_end' value={{$date_end}}>
											<input type='hidden' name='guests_amount' value={{$guests_amount}}>
											<input name="input_room_number" value={{$result->room_number}} type="hidden">
											<input name="input_cost_night" value={{$result->cost_night}} type="hidden">
											<button type="submit" class="btn btn-primary">Potwierdź</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<?php $i++; ?>
						{{-- KONIEC MODAL, KONIEC PĘTLI --}}
					@endforeach
				</tbody>
			</table>
		</div>
	</div>




<script src="/js/room_reservation.js"></script>
@endsection