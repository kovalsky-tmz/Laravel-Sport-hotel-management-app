	<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
	  <a class="navbar-brand" href="{{ url('/') }} "><b>Szkoleniowy Ośrodek Sportowy</b></a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
		<ul class="navbar-nav mr-auto nav-pills">
		  
		</ul>
		
		<ul class="nav navbar-nav navbar-right">
		
			@if (!Auth::check())
				<li class="nav-item">
					<a class="nav-link" href="{{ url('registration') }}">Zarejestruj się </a>
				</li>
			@endif

			@if(Auth::check() && (Auth::user()->role=='admin' ||  Auth::user()->role=='organizator'))
				<li class="nav-item">
					<a class="nav-link" href="{{ url('admin_registration') }}">Zarejestruj nowego użytkownika </a>
				</li>
			@endif

			@if (!Auth::check())
			  	<li class="nav-item">
					<a class="nav-link" href="{{ url('login') }}">Zaloguj </a>
		 	 	</li>
			@endif

			@if (Auth::check()) 

				<li class="nav-item">
			 		<button type="button" class="btn btn-light" onclick="location.href='{{url('reservations_in_cart')}}'">
					  Oczekujące Rezerwacje <span class="badge badge-light">{{$variable}}</span>
					</button>
				</li>

		  		<li class="nav-item">
					<div class="dropdown show">
						<a class="nav-link btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" rel="nofollow" style="color:white;">
						<span class="fa fa-user" aria-hidden="true"></span> Mój Profil ({{Auth::user()->first_name}} - <b>{{Auth::user()->role}}</b>) 
						</a>

						<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
							<a class="dropdown-item" href="{{url('reservation_options')}}">Zrób Rezerwację</a>

							@if(Auth::user()->role=='admin')
								<a class="dropdown-item" href="{{url('new_object')}}">Dodaj nowy obiekt</a>
							@endif
							@if(Auth::user()->role=='admin' || Auth::user()->role=='organizator')
								<a class="dropdown-item" href="{{url('users_list')}}">Lista użytkowników</a>
								<a class="dropdown-item" href="{{url('objects_list')}}">Lista obiektów</a>
							@endif
							@if(Auth::user()->role=='klient' )
								<a class="dropdown-item" href="{{url('my_reservations')}}">Moje Rezerwacje</a>
							@endif
							<a class="dropdown-item" href="{{url('edit_profile')}}">Edytuj Profil</a>
							<a class="dropdown-item" href="{{url('logout')}}">Wyloguj</a>
						</div>
					</div>
		 		</li>
			@endif
			

		</ul>
	  </div>
	</nav>
	