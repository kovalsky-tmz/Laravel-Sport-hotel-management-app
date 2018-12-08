@extends('layout.master')
@if (session()->has('information'))
  <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif
@include('layout.errors')
@section('content')

	<div class="row ">
		<div class="col-md-5 options" style="text-align: center;">
			<h2 style="padding-bottom: 3rem">Dodawanie nowego pokoju do hotelu sportowego</h2>
			<form method="POST" action="/new_room" >
				{{ csrf_field() }}
				<div class="form-group">
			    <label for="room_number">Numer pokoju</label>
			    <input type="text" class="form-control" required name="room_number" id="room_number" aria-describedby="room_number" pattern="\d*">
			  </div>
			  <div class="form-group">
			    <label for="max_guests">Maksymalna liczba gości</label>
			    <input type="text" class="form-control" required name="max_guests" id="max_guests" aria-describedby="max_guests" pattern="\d*">
			  </div>
			  <div class="form-group">
			    <label for="cost">Koszt za dobę</label>
			    <input type="text" class="form-control" required name="cost" id="cost" pattern="\d*">
			  </div>
			  <div class="form-group">
			    <label for="description">Opis</label>
			    <input type="text" class="form-control" required name="description" id="description">
			  </div>

			  <button type="submit" class="btn btn-primary">Stwórz</button>
			
			</form>
		</div>
	</div>

@endsection
