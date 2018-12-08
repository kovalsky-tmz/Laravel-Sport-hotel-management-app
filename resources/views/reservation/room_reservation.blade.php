@extends('layout.master')
@include('layout.errors')
@section('content')

	<div class="row">
		<div class="col-md-5 options" style="text-align:center;">
			<h2 style="padding-bottom: 4rem">Rezerwacja pokoju w hotelu sportowym</h2>
			<form method="GET" action="/room_reservation/search">
				{{ csrf_field() }}
				<div class="form-row">
					<div class="form-group col-md-6 " style="margin-bottom: 4rem">
						<label class='form-control-label' for="date_start">Od kiedy: </label>
						<input type="text" name="date_start" class="form-control datepicker" readonly='true' id="dateajax_start">
					</div>
					<div class="form-group col-md-6 " style="margin-bottom: 4rem">
						<label class='form-control-label' for="date_end">Do kiedy: </label>
						<input type="text" name="date_end" class="form-control endDatePicker" readonly='true' id="dateajax_end">
					</div>
				</div>
				<div class="form-group col-md-6 center" id="slider-guests_amount" style="margin-bottom: 1rem">
				</div>
				<p>
					<label for="amount">Liczba gości:</label>
					<input type="text" id="amount" name="slider_guests_amount" readonly style="border:0px; color:#f6931f; font-weight:bold;">
				</p>
				
				<div class="form-group col-md-8 center" id="slider-price_range" style="margin-bottom: 1rem"></div>
				<p>
				  <label for="amount_min">Przedział cenowy:</label>
				  <input type="text" id="amount_min" name="amount_min" readonly style="border:0; color:#f6931f; font-weight:bold;margin-right:-11rem">zł -
				  <input type="text" id="amount_max" name="amount_max" readonly style="border:0; color:#f6931f; font-weight:bold;margin-right:-11rem;">zł
				</p>

				<button type='submit' class='btn btn-primary btn-lg' style="margin-top: 1rem">Szukaj</button>
			</form>
		</div>	
	</div>
<script src="/js/room_reservation.js"></script>
@endsection