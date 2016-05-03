/**
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-------------------------------------------------------------
// Events
//-------------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Events = {
	jQuery: jq,

	form: '',

	addEvent: function() {
		var $ = HUB.Events.jQuery;

		$('#sub-sub-menu a').on('click', function(e){
			e.preventDefault();

			$.fancybox.open($(this).attr('href'), {
				type: 'ajax',
				width: '80%',
				height: '80%',
				maxWidth: '900',
				maxHeight: '700',
				autoSize: false,
				fitToView: true,
				afterShow: function() {
					HUB.Events.addEvent();
				}
			});
		});
	},

	initialize: function() {
		var $ = this.jQuery;

		HUB.Events.form = $('#hubForm');

		/*$('.title a').fancybox({
			type: 'ajax',
			width: '80%',
			height: '80%',
			maxWidth: '900',
			maxHeight: '700',
			autoSize: false,
			fitToView: true,
			beforeLoad: function() {
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);
			},
			afterShow: function() {
				HUB.Events.addEvent();
			}
		});*/

		if ($('#event-id') && $('#event-id').val() != '0') {
			$('#publish_up').datepicker({
				dateFormat: 'yy-mm-dd'
			});
			$('#publish_down').datepicker({
				dateFormat: 'yy-mm-dd'
			});
		} else {
			$('#publish_up').datepicker({
				altField: '#publish_down',
				dateFormat: 'yy-mm-dd'
			});
			$('#publish_down').datepicker({
				dateFormat: 'yy-mm-dd'
			});
		}

		if ($('#reccurtype0')) {
			$('#reccurtype0').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurtype1').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurtype2').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurtype3').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurtype4').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurtype5').onclick = function() {HUB.Events.checkDisable()};

			$('#reccurday_week1').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_week2').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_week3').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_week4').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_week5').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_week6').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_week7').onclick = function() {HUB.Events.checkDisable()};

			$('#reccurday_month1').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_month2').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_month3').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_month4').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_month5').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_month6').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_month7').onclick = function() {HUB.Events.checkDisable()};

			$('#reccurday_year1').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_year2').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_year3').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_year4').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_year5').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_year6').onclick = function() {HUB.Events.checkDisable()};
			$('#reccurday_year7').onclick = function() {HUB.Events.checkDisable()};

			$('#cb_wd0').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wd1').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wd2').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wd3').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wd4').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wd5').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wd6').onclick = function() {HUB.Events.checkDisable()};

			$('#cb_wn1').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wn2').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wn3').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wn4').onclick = function() {HUB.Events.checkDisable()};
			$('#cb_wn5').onclick = function() {HUB.Events.checkDisable()};

			$('#cb_wn6').onclick = function() {HUB.Events.checkDisable(this)};
			$('#cb_wn7').onclick = function() {HUB.Events.checkDisable(this)};
		}
		//$('reccurtype0').addEvent('click', HUB.Events.checkDisable());
		//document.getElementById('reccurtype0').onclick = function() {HUB.Events.checkDisable()};
	},

	checkTime: function(myField) {
		// chop leading zeros or non numeric chars from left
		// capture 4 numbers at most, 2 for hours, 2 for mins, truncate the rest
		// look for /(a,am,p,pm)/i	for std time spec in remaining string from above
		// rewrite the value of the field based on either 24hr or 12hr time formats according
		// to the events config.
		// if an illegal time format is entered, restore field value to original value before bad edit

		if (myField.name.search(/start/i) != -1) {
			name = "Start";
			chkBoxGroup = document.ev_adminForm.start_pm;
		} else {
			name = "End";
			chkBoxGroup = document.ev_adminForm.end_pm;
		}

		pmUsed = false;
		amUsed = false;
		no_hours = false;

		var time = myField.value;

		// if value begins with an optional leading 0 followed by a delimiter, assume only minutes being specified
		if (time.search(/^\s*0?[\.\-\+:]/) != -1) no_hours=true;
		time = time.replace(/[-\.,_=\+:;]/g, "");
		time = time.replace(/^\s+/,"");
		if (time.search(/^\d+/) != -1) {
			if(time.search(/^0+\D*$/) != -1) time = '0';
			// leading zeros may indicate 24 hr format
			else time = time.replace(/^0+(\d{4})/,"$1");
			time = time.replace(/\s+$/,"");
			//time = time.replace(/([^1,2]\d{2})\d+/,"$1");
			//time = time.replace(/((1|2)\d{3})\d+/,"$1");
			num = time.replace(/^(\d+).*/, "$1");

			if (num*1 <= 2359) {
				// pad the entered numer with zeros on the right to make it 4 digits
				if (no_hours) {
					num = num.replace(/^(\d)$/,"0" + "$1");
					num = '00' + num + '00';
					num = num.replace(/^(\d{4}).*$/,"$1");
				}
				num = num.replace(/^(\d)$/,"$1" + "00");
				num = num.replace(/^((1|2)\d)$/,"$1" + "00");
				num = num.replace(/^(\d\d)$/,"$1" + "0");

				if (document.all) mins = num.slice(-2);
				else mins = num.substr(-2);
				//alert('mins are: '+ mins);
				if (mins*1 < 60) {
					num *= 1;

					// need to determine here if am/pm being used
					if (time.search(/(a|p)m?$/i) != -1) {
						// using std time for entry
						// if pm, don't allow number to exceed 1200
						if (time.search(/p(m)?$/i) != -1) {
							pmUsed=true;
							if(num < 1200) num += 1200;
						} else {
							amUsed=true;
							if(num >= 1200 && num < 1300) num -= 1200;
						}
					}
					if(num < 60) hrs = '0';
					else {
						num = num + '';
						hrs = num.substr(0,num.length - mins.length);
					}
					//alert('hrs are: '+ hrs);

					// std time, convert to am/pm format, update the am/pm radio checkboxes as well
					// if am/pm was specified in the field
					if (hrs*1 > 12) {
						hrs = hrs*1 -12 + '';
						if (pmUsed) {
							//adjust radio checkboxes
							chkBoxGroup[0].checked = false;
							chkBoxGroup[1].checked = true;
						}
					}
					if (amUsed) {
						chkBoxGroup[0].checked = true;
						chkBoxGroup[1].checked = false;
					}
					if (hrs*1 == 0) hrs = 12;
					time = hrs + ':' + mins;

					// sucessful field edit.  update the old field value with the new one
					myField.oldValue = myField.value;
					myField.value = time;
					return true;
				}
			}
		}
		// bad input format, alert user, reset field value
		if(myField.name.search(/start/i) != -1) name = "Start";
		else name = "End";
		alert('Bad ' + name + ' Time format: ' + myField.value + '\nValid format is hh:mm {am|pm} (12 or 24hr format).  Please try again.');
		if(myField.oldValue) myField.value = myField.oldValue;
		else myField.value = '';
		window.globalObj = myField;
		var t = setTimeout('window.globalObj.focus();',100);
		return true;
	},

	checkPublish: function() {
		var form = HUB.Events.form;
		if (form.publish_down.value < form.publish_up.value) {
			form.publish_down.value = form.publish_up.value;
		}
		checkDisable();
	},

	checkRepeatValues: function() {
		var form = HUB.Events.form;
		var f = form.reccurtype;
		if (recurtval >= 0) {
			f[recurtval].checked = true;
		}

		if ((recurtval == 1) || (recurtval == 2)) {
			var g = document.ev_adminForm;
			if (recurwval == "pair") {
				g.cb_wn6.checked = true;
			}
			if (recurwval == "impair") {
				g.cb_wn7.checked = true;
			}
		}
	},

	checkSelectedWeeks: function() {
		var form = HUB.Events.form;
		if ((form.reccurtype[1].checked==true) || (form.reccurtype[2].checked==true)) {
			var check = 0;
			for (i=1; i < 8; i++)
			{
				cb = eval( 'form.cb_wn' + i );
				if (cb.checked==true) {
					check++;
				}
			}
			return check;
		}
	},

	checkSelectedDays: function() {
		var form = HUB.Events.form;
		if (form.reccurtype[5].checked==true) {
			var f = form.reccurday_year;
			var check = 0;
			for (i=0; i < f.length; i++)
			{
				if (f[i].checked==true) {
					check++;
				}
			}
			return check;
		}
		if (form.reccurtype[3].checked==true) {
			var f = form.reccurday_month;
			var check = 0;
			for (i=0; i < f.length; i++)
			{
				if (f[i].checked==true) {
					check++;
				}
			}
			return check;
		}
		if (form.reccurtype[1].checked==true) {
			var f = form.reccurday_week;
			var check = 0;
			for (i=0; i < f.length; i++)
			{
				if (f[i].checked==true) {
					check++;
				}
			}
			return check;
		}
		if (form.reccurtype[2].checked==true) {
			var check = 0;
			for (i=0; i < 7; i++)
			{
				cb = eval( 'form.cb_wd' + i );
				if (cb.checked==true) {
					check++;
				}
			}
			return check;
		}
	},

	checkDisable: function(control) {
		var form = HUB.Events.form;
		// Check repeat Disable repeat option
		if (form.publish_down.value == form.publish_up.value) {
			var f = form.reccurtype;
			for (i=1; i < f.length; i++)
			{
				// dmcd May 7/04 commented out this disable.  It confuses people
				f[i].disabled = false;
			}
			//form.reccurtype[0].checked=true;
		} else {
			var f = form.reccurtype;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = false;
			}
		}
		// By Week : Check reccurday
		if (form.reccurtype[1].checked==true) {
			var f = form.reccurday_week;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = false;
			}
		} else {
			var f = form.reccurday_week;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = true;
			}
		}
		// By Week : Check weekdays
		if (form.reccurtype[2].checked==true) {
			var f = HUB.Events.form;
			for (i=0; i < 7; i++)
			{
				cb = eval( 'f.cb_wd' + i );
				cb.disabled = false;
			}
		} else {
			var f = HUB.Events.form;
			for (i=0; i < 7; i++)
			{
				cb = eval( 'f.cb_wd' + i );
				cb.disabled = true;
			}
		}
		// By Week : Disable Weeks select
		if ((form.reccurtype[1].checked==true) || (form.reccurtype[2].checked==true)) {
			var g = HUB.Events.form;
			for (i=1; i < 8; i++)
			{
				cb = eval( 'g.cb_wn' + i );
				cb.disabled = false;
			}
			if (control && (control.id == "cb_wn6" || control.id == "cb_wn7")) {
				// dmcd oct 4/04  uncheck all of the month weeks
				for (i=1; i < 6; i++)
				{
					cb = eval( 'g.cb_wn' + i );
					cb.checked = false;
				}
			} else if (control && control.id.search(/^cb_wn[0-9]+$/i) != -1 && control.checked) {
				// dmcd oct 4/04  uncheck the even/odd week radio boxes
				g.cb_wn6.checked = false;
				g.cb_wn7.checked = false;
			}
		} else {
			var g = HUB.Events.form;
			for (i=1; i < 8; i++)
			{
				cb = eval( 'g.cb_wn' + i );
				cb.disabled = true;
			}
		}
		// By Month : Check reccurday
		if (form.reccurtype[3].checked==true) {
			var f = form.reccurday_month;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = false;
			}
		} else {
			var f = form.reccurday_month;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = true;
			}
		}
		// By Year : Check reccurday
		if (form.reccurtype[5].checked==true) {
			var f = form.reccurday_year;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = false;
			}
		} else {
			var f = form.reccurday_year;
			for (i=0; i < f.length; i++)
			{
				f[i].disabled = true;
			}
		}
	},
	preventDelete: function() {
		$('.delete').click(function(event){
			var del = confirm('Are you sure you would like to delete this event?');
			if (!del)
			{
				event.preventDefault();
			}
		});
	}

}

jQuery(document).ready(function($){
	HUB.Events.initialize();
	HUB.Events.preventDelete();
	if ($('.prior').length != 0) {
		$('#toggle-prior-anchor').click(function() { 
			if ($('#toggle-prior-anchor').text() == 'Show Past Events') {
				$('#toggle-prior-anchor').text('Hide Past Events');
			} else {
				$('#toggle-prior-anchor').text('Show Past Events');
			}
			$('.prior').slideToggle(500);
		});
		$('.prior').slideUp(500);
	} else {
		$('#toggle-prior-anchor').hide();
	}
});
