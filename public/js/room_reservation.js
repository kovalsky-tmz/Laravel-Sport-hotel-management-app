$(document).ready( function() {					//OKIENKO DO DATY
	$( ".datepicker" ).datepicker({
		showOn: "both",
		buttonImage: "/images/calendar.gif",
		buttonImageOnly: true,
		buttonText: "Wybierz date",
		minDate: new Date(),
		dateFormat: 'yy-mm-dd',
		onSelect: function(date){				// DLA DATY ZAKONCZENIA, ZEBY NIE BYLO WCZESNIEJ NIZ DATA ROZPOCZECIA

	        var selectedDate = new Date(date);
	        var msecsInADay = 86400000;
	        var endDate = new Date(selectedDate.getTime() + msecsInADay);

	        $(".endDatePicker").datepicker( "option", "minDate", endDate );
	        $(".endDatePicker").datepicker( "option", "maxDate", '+2y' );
    }
	});	

	$(".endDatePicker").datepicker({ 	// DATA ZAKONCZENIA
	   	showOn: "both",
		buttonImage: "/images/calendar.gif",
		buttonImageOnly: true,
		buttonText: "Wybierz date",
		minDate: 0,
		dateFormat: 'yy-mm-dd',
	});


	$( "#slider-guests_amount" ).slider({
		range: "max",
		min: 1,
		max: 8,
		value: $('#amount').val(),
		slide: function( event, ui ) {
		$( "#amount" ).val( ui.value );
		}
	});
	$( "#amount" ).val( $( "#slider-guests_amount" ).slider( "value" ) );		//wartosc ze slidera
});

$( function() {
	$( "#slider-price_range" ).slider({
		range: true,
		min: 40,
		max: 250,
		values: [ $('#amount_min').val(), $('#amount_max').val() ],
		slide: function( event, ui ) {
			 $("#amount_min").val(ui.values[0]);
          	 $("#amount_max").val(ui.values[1]);
		}
	});
      $("#amount_min").val($("#slider-price_range").slider("values", 0));
      $("#amount_max").val($("#slider-price_range").slider("values", 1));
      $("#amount_min").change(function() {
        $("#slider-price_range").slider("values", 0, $(this).val());
      });
      $("#amount_max").change(function() {
        $("#slider-price_range").slider("values", 1, $(this).val());
      })
    });