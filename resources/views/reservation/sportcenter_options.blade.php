@extends('layout.master')
@include('layout.errors')
@section('content')
	<div class="col col-md-7 options">
		<form method="GET" action="">
			<button type="submit" class="btn btn-secondary btn-lg btn-block button_sportcenter_options"><i class="" aria-hidden="true"></i> Piłka Nożna</button>
		</form>

		<form method="GET" action="">
			<button type="submit" class="btn btn-light btn-lg btn-block option button_sportcenter_options">Siatkówka Plażowa <i class="" aria-hidden="true"></i></button>
		</form>

		<form method="GET" action="">
			<button type="submit" class="btn btn-secondary btn-lg btn-block option button_sportcenter_options"><i class="" aria-hidden="true"></i> Tenis</button>
		</form>

		<form method="GET" action="">
			<button type="submit" class="btn btn-light btn-lg btn-block option button_sportcenter_options"><i class="" aria-hidden="true"></i>Hala Sportowa</button>
		</form>
		
	</div>
@endsection