

	$(document).ready( function() {					//OKIENKO DO DATY
		$( ".datepicker" ).datepicker({
			showOn: "both",
			buttonImage: "/images/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Wybierz date",
			minDate: 0,
			dateFormat: 'yy-mm-dd'
    	});	
    	
	});


		$('#dateajax').on('change', function () {		// AJAX na zmiane inputa godzinowego po wybieraniu dnia
			$('.hours').empty();	
			$('#submit,#hours_amount,#guests_amount,#busy,#hours').empty();			// zerowanie wyboru godziny
			var date = $('#dateajax').val();
			var name = $('#dateajax').data('name');
        	$.ajax({
	            type: "GET",
	            url: '/solo_reservation/'+name+'/'+date,
	           	dataType: 'json',
	            success: function( data ) {
	            	console.log(data);
	            	var single_hour = data.free.split(","); // zamiana ajax response DATA na tablice single_hour
	            	var single_text =data.text.split(",");	// zamiana ajax respone DATA na tablice single_text (2 argumenty)
	            	var i=0;
	            	$('.hours').append($("<label class='form-control-label' for='hours'>Wybierz Godzinę: </label> <select class='custom-select form-control' id='free_hours' name='hours[]' multiple='multiple' size='5' style='height: 18rem;width: 22rem;display:block;text-align:center'>").fadeIn('400'))
	            	while(single_hour[i]!=null){			// TWORZENIE GODZIN z tablicy
		                $('#free_hours').append($('<option></option>').attr("value",single_hour[i]).text(single_hour[i] + single_text[i])); 
						i++;
					};
            	}
        	});
    	});



		$('.hours').on('change','#free_hours', function(){   // Tworzenie inputa po wybraniu godziny !
			$('#submit,#hours_amount,#guests_amount,#busy,#hours').empty();
			var $free_hours=$('#free_hours');
			var $a=$free_hours.val(); // wartosc przekazana przez controller do AJAX, potrzebna do indexOf('Godzina Zajęta')
			var $text=$("#free_hours option:selected").text(); // text zaznaczonej opcji
			if((($text.indexOf('Brak'))<=-1) && ($text!="Godzina rezerwacji")){

				// $('#hours').append($("<div id='slider-hours'></div><label for='hour_amount'>Ile wejść? : &nbsp</label><input type='text' class=' form-group' id='hour_amount' name='slider-hours' readonly style='margin-top: 25px; border:0; color:#f6931f;'>").fadeIn('400'));
				$('#guests_amount').append($("<div id='slider-guests_amount'></div><label for='amount'>Ile osób? : &nbsp</label><input type='text' class=' form-group' id='amount' name='slider-guests_amount' readonly style='margin-top: 25px; border:0; color:#f6931f;'>").fadeIn('400'));		
															// dodanie div -slider		
				$( function() {								// FUNKCJA TWORZY SLIDER (nie wiem jak inaczej)
				    $( "#slider-guests_amount" ).slider({
				      range: "max",
				      min: 1,
				      max: 30,
				      value: 1,
				      slide: function( event, ui ) {
				        $( "#amount" ).val( ui.value );
				      }
				    });
				    $( "#amount" ).val( $( "#slider-guests_amount" ).slider( "value" ) );		//wartosc ze slidera
				} );


				$( function() {								// FUNKCJA TWORZY SLIDER (nie wiem jak inaczej)
				    $( "#slider-hours" ).slider({
				      range: "max",
				      min: 1,
				      max: 10,
				      value: 1,
				      slide: function( event, ui ) {
				        $( "#hour_amount" ).val( ui.value );
				      }
				    });
				    $( "#hour_amount" ).val( $( "#slider-hours" ).slider( "value" ) );		//wartosc ze slidera
				} );


				$('#submit').append($("<button type='submit' class='btn btn-primary btn-lg button_option'>Zarezerwuj miejsca</button>").fadeIn('400'));
			}else if((($text.indexOf('Brak'))>=0) && ($text!="Godzina rezerwacji")){
				$('#busy').append($("<div class='alert alert-primary' role='alert'> Niestety, na tę godzinę brakuje miejsc.</div>").fadeIn('400'));
			};
		});
