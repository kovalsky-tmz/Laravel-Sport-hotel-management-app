@extends('layout.master')
@section('content')
	<div class="col col-md-7 options">
		@foreach($solo as $solo)
			
			<form method="GET" action="{{url('object/solo/'.$solo->object_name)}}">
				<button type="submit" class="btn btn-light btn-md btn-block button_option"><i class="fa fa-bolt" aria-hidden="true"></i>{{str_replace('_', ' ', $solo->object_name)}}</button>
			</form>
		@endforeach

		@foreach($group as $group)
			
			<form method="GET" action="{{url('object/group/'.$group->object_name)}}">
				<button type="submit" class="btn btn-light btn-md btn-block button_option"><i class="fa fa-futbol-o" aria-hidden="true"></i>{{str_replace('_', ' ', $group->object_name)}}</button>
			</form>
		@endforeach
		{{-- <form method="GET" action="{{url('sportcenter_options')}}">
		<button type="submit" class="btn btn-secondary btn-lg btn-block button_option"><i class="fa fa-futbol-o" aria-hidden="true"></i> Rezerwacja Boisk</button>
		</form> --}}

		<form method="GET" action="{{url('object/hotel')}}">
		<button type="submit" class="btn btn-light  btn-md btn-block option button_option " > Hotel Sportowy <i class="fa fa-bath" aria-hidden="true"></i></button>
		</form>

		{{-- <form method="GET" action="{{url('solo_reservation/gym')}}">
		<button type="submit" class="btn btn-secondary btn-lg btn-block option button_option"><i class="fa fa-bolt" aria-hidden="true"></i> Rezerwacja Siłowni</button>
		</form> --}}
		
	</div>
@endsection