@extends('layout.master')
@include('layout.errors')
@section('content')

	<div class="row"  style='padding-bottom: 3rem;margin-top: 8rem;background-color: white;border: 1px solid #e6e6e6;'>
		<div class="col-md-5 options" style="text-align:center;">
			<h2 style="padding-bottom: 4rem">Rezerwacja grupowa dla obiektu {{str_replace('_', ' ', $name)}}</h2>
			<form method="GET" action="/group_reservation/search/{{$name}}">
				{{ csrf_field() }}
				<div class="form-row">
					<div class="form-group col-md-12 " style="margin-bottom: 2rem">

						<div class="form-group">
							<label class='form-control-label' for="date_start">Na kiedy: </label>
							<input type="text" name="date_start" class="form-control datepicker" readonly='true' id="dateajax_start">
						</div>

						<div class="form-group">
							<label for="field_type"><b>Typ boiska:</b></label>

							<select class="form-control" name="field_type" id="field_type" data-name='{{$name}}' style="margin-bottom: 2rem">
								@foreach($fields as $field)
									<option value="{{$field->field_type}}" data-cost="{{$field->cost_per_entrance}}" data-desc="{{$field->description}}" id="onchange"> {{$field->field_type}}</option>
								@endforeach
							</select>

							<div id="cost"></div>
							<p></p>
							<div id="description"></div>
						</div>
						<div class="form-group" style="margin-top: 2rem">
							<label for="open_days">Godziny otwarcia:</label>
							<div name="open_days" id="open_days" ></div>
							
						</div>
					</div>
				</div>


				<button type='submit' class='btn btn-primary btn-lg' style="margin-top: 0rem">Wyświetl dostępne godziny</button>
			</form>
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
			dateFormat: 'yy-mm-dd',

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