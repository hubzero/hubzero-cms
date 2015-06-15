//jquery no-conflict mode
var $jQ = jQuery.noConflict();

//on document ready
$jQ(document).ready(function() {
	//get the start and end dates
	var start = $jQ('#date_start').val();
	var end = $jQ('#date_end').val();
	
	//reformat dates for the visible textbox
	var ps = start.split("-");
	var pe = end.split("-");
	var s = ps[1] + "/" + ps[2] + "/" + ps[0];
	var e = pe[1] + "/" + pe[2] + "/" + pe[0];
	
	//counter to hold clicks on datepicker
	var counter = 0;
	
	//setup datepicker
	$jQ('#page_view_dates').DatePicker({
		format: 'm/d/Y',
		date: [ s, e ],
		calendars: 2,
		start: 1,
		mode: 'range',
		onChange: function(formated, dates) {
			
			//increment counter
			counter++;
			
			//get current date and make it today at 11:59:59pm so user can select today on datepicker
			var now = new Date();
			var nowMidnight = Date.UTC( 
				now.getFullYear(), now.getMonth(), now.getDate(), 
				'23', '59', '59'
			);
			
			//create UTC version of dates we they can be compared and made sure they are less then today
			var dateStart = Date.UTC(
				dates[0].getFullYear(), dates[0].getMonth(), dates[0].getDate(), 
				dates[0].getHours(), dates[0].getMinutes(), dates[0].getSeconds()
			);
			var dateEnd = Date.UTC(
				dates[1].getFullYear(), dates[1].getMonth(), dates[1].getDate(), 
				dates[1].getHours(), dates[1].getMinutes(), dates[1].getSeconds()
			);
			
			//split the formated version of the start/end dates that the datepicker returns
			var p_s = formated[0].split("/");
			var p_e = formated[1].split("/");
			
			//format start/end date for hidden input (YYYY-mm-dd)
			var start = p_s[2] + "-" + p_s[0] + "-" + p_s[1];
			var end	= p_e[2] + "-" + p_e[0] + "-" + p_e[1];
			
			//if user has selected a start and end date (clicked twice)
			if(counter %2 == 0) {
				//if dates selected are both today or earlier
				if(dateStart > now || dateEnd > nowMidnight) {
					//alert user their selections arent correct
					//clear selections
					alert('You must select an earlier start and end date');
					$jQ('#page_view_dates').DatePickerClear();
				} else {
					//set the visible textbox
					//set the two hidden inputs sent in form
					$jQ('#page_view_dates').val( formated[0] + " - " + formated[1] );
					$jQ('#date_start').val(escape(start));
					$jQ('#date_end').val(escape(end));
				}
			}
			
		}
	});
	
	
});