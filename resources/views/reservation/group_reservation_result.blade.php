@extends('layout.master')
@include('layout.errors')
@if (session()->has('information'))
  <div class="col-md-10 alert alert-success" style="margin-top: 15px"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
@endif
@section('content')

	<div class="row" style='margin-top: 2rem'>
		
		<div class="col-md-5 browser">

			<div class="col-md-10 options" style="text-align:center;">

				<form method="GET" action="/group_reservation/search/{{$name}}">
					{{ csrf_field() }}
					
					<div class="form-group col-md-6 " style="margin-bottom: 2rem">
						<label class='form-control-label' for="date_start">Od kiedy: </label>
						<input type="text" name="date_start" class="form-control datepicker" readonly='true' id="dateajax_start" value="{{$date_start}}" >
					</div>

					<div class="form-group col-md-6 " style="margin-bottom: 2rem">
						<label for="field_type"><b>Typ boiska:</b></label>

						<select class="form-control" name="field_type" id="field_type" data-name='{{$name}}' style="margin-bottom: 2rem">
							@foreach($fields as $field)
								@if(($field_type)==($field->field_type))
									<option selected value="{{$field->field_type}}" data-cost="{{$field->cost_per_entrance}}" data-desc="{{$field->description}}" id="onchange"> {{$field->field_type}}</option>
								@else
									<option value="{{$field->field_type}}" data-cost="{{$field->cost_per_entrance}}" data-desc="{{$field->description}}" id="onchange"> {{$field->field_type}}</option>
								@endif
								
							@endforeach
						</select>

						<div id="cost"></div>
						<p></p>
						<div id="description"></div>
						<div class="form-group" style="margin-top: 2rem">
							<label for="open_days">Godziny otwarcia:</label>
							<div name="open_days" id="open_days" ></div>
						</div>
					</div>

					<div class="form-group col-md-6 " style="margin-bottom: 2rem">	
					<button type='submit' class='btn btn-primary btn-lg' style="margin-top: 0rem">Znajdź wolne godziny</button>
					</div>

				</form>

			</div>
		</div>

		<div class="col-md-6 results_browser">
			<table class="table table-hover">
				<thead>
					<tr>
						
						<th>Dostępne wolne godziny</th>
						<th>Opcje</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=0; ?>
					@foreach($enter as $enter)  

						<tr>
							
							@if(strpos($enter, 'zajęta') == false)
								<td style="text-align: left">{{$enter}}</th>
								<td ><button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#reserveModal{{$i}}">Zarezerwuj</button></td>
							@else
								<td class="bg-light" style="text-align: left">{{$enter}}</th>
								<td	class="bg-light"></td>
							@endif
						</tr>

						{{-- MODAL DLA REZERWACJI, CZY NA PEWNO CHCE REZERWOWAC --}}
						<div class="modal fade" id="reserveModal{{$i}}" tabindex="-1" role="dialog" aria-labelledby="reserveModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="reserveModalLabel">Potwierdzenie</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										  <span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										Czy na pewno chcesz zarezerwować następujące boisko?:<p><p>Typ Boiska: {{$field_type}}<br>Numer boiska: {{$field_number}} <br>Cena: {{$cost}}<br>Data: {{$date_start}} {{$enter}}
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
										<form method="POST" action="/group_reservation/reserve">
											{{ csrf_field() }}
											<input type='hidden' name='name' value={{$name}}>
											<input name="cost" value={{$cost}} type="hidden"> 
											<input name="date_start" value="{{$date_start}} {{$enter}}" type="hidden"> 
											<input name="field_number" value={{$field_number}} type="hidden">


											<button type="submit" class="btn btn-primary submit_stop">Potwierdź</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<?php $i++; ?>
						{{-- KONIEC MODAL, KONIEC PĘTLI --}}
					@endforeach
				</tbody>
			</table>
		</div>
	</div>



<script>
	$(document).ready( function() {					//OKIENKO DO DATY
		$( ".datepicker" ).datepicker({
			showOn: "both",
			buttonImage: "/images/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Wybierz date",
			minDate: 0,
			dateFormat: 'yy-mm-dd'
    	});	

		var $cost=$('#field_type option:selected').data('cost');
		var $desc=$('#field_type option:selected').data('desc');
		$('#cost').html("Koszt dla tego typu boiska:<br><span style='color:#009933;font-weight: 700;'> "+$cost);
    	$('#description').html("Opis boiska:<br><span style='color:#009933;font-weight: 700;'> "+$desc);

    	$("#field_type").on('change',function(){
    		var $cost=$('#field_type option:selected').data('cost');
    		var $desc=$('#field_type option:selected').data('desc');
    		$('#cost').html("Koszt dla tego typu boiska:<br><span style='color:#009933;font-weight: 700;'> "+$cost);
    		$('#description').html("Opis boiska:<br><span style='color:#009933;font-weight: 700;'> "+$desc);

    	});

    	///////////////////////// AJAX
    	var $field_name= $('#field_type').val();
        var $name=$('#field_type').data('name');
    	$.ajax({
                type: "GET",
                url: '/group_reservation/check_day/'+$name+'/'+$field_name,
                dataType: 'JSON',
                success: function(data){
                	console.log(data.days[0]);
                	var i=0;
                	var j=0;
                	while(data.days[i]!=null){			// TWORZENIE GODZIN z tablicy
		                $('#open_days').append("<span class='special_font'>"+data.days[i]+'<br>');
		                
		                $('#open_days').append(data.hours[j]+'</span>'+'<br>');
		                j++;
						i++;
					};
                }
            });
    	///////////////// ON CHANGE
        $('#field_type').on('change',function(){
        	var $field_name= $('#field_type').val();
        	var $name=$('#field_type').data('name');
        	$('#open_days').empty();
            $.ajax({
                type: "GET",
                url: '/group_reservation/check_day/'+$name+'/'+$field_name,
                dataType: 'JSON',
                success: function(data){
                	console.log(data.days[0]);
                	var i=0;
                	var j=0;
                	while(data.days[i]!=null){			// TWORZENIE GODZIN z tablicy
		                $('#open_days').append("<span class='special_font'>"+data.days[i]+'<br>');
		                
		                $('#open_days').append(data.hours[j]+'</span>'+'<br>');
		                	j++;
						i++;
					};
                }
            });
        });

	});
</script>

@endsection