@extends('layout.master')
@section('content')

	@include('layout.errors')
	<div class="col-md-8 col-md-offest-4 center" style='padding-bottom: 3rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;'>
		<h1 style="padding-bottom:2rem;margin-top: 3rem">Edytuj Profil</h1>
		<form method='POST' action='/edit_profile'>
		
			{{ csrf_field() }}
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" class="form-control" required name="email" id="email" placeholder="Example@gmail.com" value={{ $user->email }}>
					</div>

					<div class="form-group">
						<label for="password">Nowe Hasło:</label>
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
						<input type="text" class="form-control" name="first_name" id="first_name" value={{$user->first_name }}>
					</div>
					<div class="form-group">
						<label for="last_name">Nazwisko:</label>
						<input type="text" class="form-control" required name="last_name" id="last_name" value={{ $user->last_name }}>
					</div>
					<div class="form-group">
						<label for="phone">Telefon:</label>
						<input type="text" class="form-control" required name="phone" id="phone" value={{ $user->phone }}>
					</div>
					<div class="form-group">
						<label for="city">Miasto:</label>
						<input type="text" class="form-control" name="city" id="city" value={{ $user->city }}>
					</div>
					
				</div>

			</div>
			<div class="form-group" style="margin-left: 40%;margin-top: 1rem" >
					<button type="submit" class="btn btn-primary">Zatwierdź zmiany!</button>
			</div>
		</form>
		
			
		
	</div>
			
			{{-- <div class="form-group">
				@include('layouts.errors')
			</div> --}}
		
	


@endsection