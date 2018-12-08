@extends('layout.master')
@section('content')

	<div class="col-md-8 col-md-offest-4 center">
		<h1 style="padding-bottom:2rem;margin-top: 3rem">Rejestracja</h1>
		<form method='POST' action='/register'>
		
			{{ csrf_field() }}
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" class="form-control" required name="email" id="email" placeholder="Example@gmail.com" value={{ old('email') }}>
					</div>

					<div class="form-group">
						<label for="password">Hasło:</label>
						<input type="password" class="form-control" required name="password" id="password" placeholder="Minimum 6 znaków" >
					</div>

					<div class="form-group">
						<label for="password_confirmation">Potwierdzenie Hasła:</label>
						<input type="password" class="form-control" required name="password_confirmation" id="password_confirmation" placeholder="Potwierdź hasło">
					</div>

				</div>

				<div class="col-md-6">

					<div class="form-group">
						<label for="first_name">Imię:</label>
						<input type="text" class="form-control" name="first_name" id="first_name" value={{ old('first_name') }}>
					</div>
					<div class="form-group">
						<label for="last_name">Nazwisko:</label>
						<input type="text" class="form-control" required name="last_name" id="last_name" value={{ old('last_name') }}>
					</div>
					<div class="form-group">
						<label for="phone">Telefon:</label>
						<input type="text" class="form-control" required name="phone" id="phone" value={{ old('phone') }}>
					</div>
					<div class="form-group">
						<label for="city">Miasto:</label>
						<input type="text" class="form-control" name="city" id="city" value={{ old('city') }}>
					</div>
					
				</div>

			</div>
			<div class="form-group" style="margin-left: 40%;margin-top: 1rem" >
					<button type="submit" class="btn btn-primary">Zarejestruj!</button>
			</div>
		</form>
		
			@include('layout.errors')
		
	</div>
			
			{{-- <div class="form-group">
				@include('layouts.errors')
			</div> --}}
		
	


@endsection