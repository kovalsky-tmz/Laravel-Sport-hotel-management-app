@extends('layout.master')
@include('layout.errors')
@section('content')
@if (session()->has('information'))
      <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif
<div class="col-md-11 center " style="margin-top: 3rem">
	@if($role=='klient')
		<h3>Aktywne rezerwacje użytkownika na obiekcie {{str_replace('_', ' ', $name)}}</h3>
	@else
		<h3>Aktywne akcje administracyjne na obiekcie {{str_replace('_', ' ', $name)}}</h3>
	@endif

	<table class="table table-hover" style="font-size:14px;">
			<?php $i=0;?>
			<thead >
				<tr>
					<th>ID<br> Rezerwacji</th>
					<th>Na Nazwisko</th>
					<th>Na Email</th>
					@if($role=='klient')
						<th>Całkowity koszt rezerwacji</th>
					@endif
	{{-- 				<th>Na Nazwisko</th>
					<th>Na Email</th>
					<th>Początek rezerwacji</th>
					<th>Koniec rezerwacji</th>--}}
					<th>Uwagi</th>
					<th>Do dopłaty</th>
					<th>Opcje </th> 
				</tr>
			</thead>
			@foreach($reservation as $reservation)
				<?php $cost=0;?>

				@foreach($solos as $solo)
					@if($solo->reservation_id==$reservation->reservation_id)
						<?php $cost+=($solo->guests_amount*$solo->cost_hour); ?> 
					@endif
				@endforeach
				
					<tbody>
						<tr>
							<td><button type="button" class="btn btn-light btn-sm rozwin" value="{{$reservation->reservation_id}}" title="Rozwiń"><i class="fa fa-plus-square" aria-hidden="true"></i></button>{{$reservation->reservation_id}}</td>
							<td>{{$reservation->user->last_name}}</td>
							<td>{{$reservation->user->email}}</td>
							@if($role=='klient')
								<td>
									{{$cost}}
								</td>
							@endif
							<td><span class="special_font">{{$reservation->comment}}</span></td>
							<td>{{$reservation->total_cost}}zl</td>
							<td>		
								@if($reservation->event==null)		
									<button type="button" class="btn btn-light btn-sm" title="Aktywuj" data-toggle="modal" data-target="#activate{{$reservation->reservation_id}}" ><i class="fa fa-check-square-o" aria-hidden="true"></i></button> 
								@endif			
								<button type="button" class="btn btn-light btn-sm" title="Usuń"  onclick="location.href='{{url('/users_list/active_reservations/solo/remove/'.$name.'/'.$reservation->user_id.'/'.$reservation->reservation_id)}}'" ><i class="fa fa-trash" aria-hidden="true"></i></button> 
							</td>
						</tr>
					</tbody>
					<thead class="{{$reservation->reservation_id}}" style="font-size:10px; display: none;">
							<tr>
								<th>Początek rezerwacji</th>
								<th>Koniec rezerwacji</th>
								@if($role=='klient')
									<th>Ilość osób</th>
									<th>Koszt<br>za rezerwację</th>
									<th>Koszt<br>na osobę</th>
								@endif
								
							</tr>
					</thead>

					<?php $id=$reservation->reservation_id;?> {{-- żeby wyświetlało tylko aktualne RESERV ID--}}

					@foreach($active_reservations as $active_reservation)
						@if($active_reservation->reservation_id==$id) {{-- warunek - żeby wyświetlało tylko aktualne RESERV ID--}}
						<tbody class="{{$active_reservation->reservation_id}}" style=" font-size:10px; display: none;">
							<tr>
								<td><b>{{$active_reservation->reservation_start}}</b></td>
								<td><b>{{$active_reservation->reservation_end}}</b></td> 
								@if($role=='klient')
									<td>{{$active_reservation->guests_amount}}</td>
									<td><?php echo ($active_reservation->cost_hour)*($active_reservation->guests_amount).'zl' ?></td>
									<td>{{$active_reservation->cost_hour}}</td>
								@endif
								
							</tr>
						</tbody>
						@endif
					@endforeach
				<!-- Modal -->
				<div class="modal fade" id="activate{{$reservation->reservation_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Aktywacja rezerwacji {{$reservation->reservation_id}}</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				      	<form method="POST" action="/reservation_activate">
					      	{{ csrf_field() }}
					      	<input type='hidden' name='reservation_id' value={{$reservation->reservation_id}}>
					      	<div class="row">
					      		<div class="col-md-6">
					      				<div class="form-group">
					      					<div class="form-inline col-md-* " style="margin-bottom: 1rem">
					      						<p>Do zapłaty: <span class="special_font">{{$reservation->total_cost}}</span></p>
												<label class='form-control-label' for="comment">Uwagi do aktywacji: </label>
												<input type="text" name="comment" class="form-control" id="comment">
												
											</div>
							
											<div class="form-inline col-md-* " style="margin-bottom: 0rem">
												<label class='form-control-label' for="paid">Wpłacono: </label>
												<input type="number" name="paid" class="form-control" id="paid">
											</div>
					      			</div>
					      		</div>
					      	</div>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
					        <button type="submit" class="btn btn-primary">Zapisz</button>
					      </div>
				      </form>
				    </div>
				  </div>
				</div>

			@endforeach
		
	</table>
</div>

<script>
	$(document).ready(function(){
		$('.rozwin').on('click',function(){
			var $value=$(this).val();
			if($('.'+$value).css('display')=='none'){
				$(this).find(".fa-plus-square").toggleClass('fa-plus-square fa-minus');
				$('.'+$value).show();

				
			}else{
				$(this).find(".fa-minus").toggleClass('fa-minus fa-plus-square');
				$('.'+$value).hide();
				

			};
		});
	})
</script>
@endsection