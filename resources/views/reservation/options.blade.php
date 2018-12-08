@extends('layout.master')
@section('content')
	<div class="col col-md-7 options">
		@foreach($solo as $solo)
			
			<form method="GET" action="{{url('solo_reservation/'.$solo->object_name)}}">
				<button type="submit" class="btn btn-info btn-lg btn-block button_option"><i class="fa fa-bolt" aria-hidden="true"></i> Rezerwacja {{str_replace($days, ' ', $solo->object_name)}}</button>
			</form>
		@endforeach

		@foreach($group as $group)
			
			<form method="GET" action="{{url('group_reservation/'.$group->object_name)}}">
				<button type="submit" class="btn btn-info btn-lg btn-block button_option"><i class="fa fa-futbol-o" aria-hidden="true"></i> Rezerwacja {{str_replace('_', ' ', $group->object_name)}}</button>
			</form>
		@endforeach
		{{-- <form method="GET" action="{{url('sportcenter_options')}}">
		<button type="submit" class="btn btn-secondary btn-lg btn-block button_option"><i class="fa fa-futbol-o" aria-hidden="true"></i> Rezerwacja Boisk</button>
		</form> --}}

		<form method="GET" action="{{url('room_reservation')}}">
		<button type="submit" class="btn btn-info  btn-lg btn-block option button_option " >Rezerwacja Hotelu <i class="fa fa-bath" aria-hidden="true"></i></button>
		</form>

		{{-- <form method="GET" action="{{url('solo_reservation/gym')}}">
		<button type="submit" class="btn btn-secondary btn-lg btn-block option button_option"><i class="fa fa-bolt" aria-hidden="true"></i> Rezerwacja Siłowni</button>
		</form> --}}
		
	</div>
@endsection