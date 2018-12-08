
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
	        var msecsInADay = 43200000;
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


});