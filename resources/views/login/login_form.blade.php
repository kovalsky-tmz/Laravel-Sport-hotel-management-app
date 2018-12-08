@extends('layout.master')
@include('layout.errors')
@section('content')
	<div class="col-md-4 col-md-offest-4 center" style='padding-bottom: 3rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;max-width: 25rem;'>
		<h2 style="padding-bottom: 2rem;margin-top: 3rem">Zaloguj się</h2>

		<form method='POST' action='/login'>
			{{ csrf_field() }}
			
			<div class="form-group">
				<label for="email">Email:</label>
				<input type="email" class="form-control" required name="email" id="email">
			</div>

			<div class="form-group">
				<label for="password">Hasło:</label>
				<input type="password" class="form-control" required name="password" id="password">
			</div>

			<div class="form-group" style="margin-left: 38%; margin-top: 2rem">
				<button type="submit" class="btn btn-primary">Zaloguj</button>
			</div>
			
		</form>
	</div>

	
@endsection