/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

Joomla.submitbutton = function(task) {
	if (task == 'preview') {
		var id = '',
			ids = document.getElementsByName('id[]');
		for (var i=0; i< ids.length;i++)
		{
			if (id == '' && ids[i].type == 'checkbox' && ids[i].checked)
			{
				id = parseInt(ids[i].value);
			}
		}

		HUB.Administrator.Newsletter.newsletterPreview(id);
		return;
	}

	if (task == 'stop') {
		var message = document.getElementById('admin-form').getAttribute('data-confirm-stop');
		if (!confirm(message)) {
			return;
		}
	}

	if (task == 'dosendnewsletter') {
		// check to make sure we all set to go
		if (!HUB.Administrator.Newsletter.sendNewsletterCheck()) {
			return;
		}

		// double check with user
		if (!HUB.Administrator.Newsletter.sendNewsletterDoubleCheck()) {
			return;
		}
	}

	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Joomla.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!HUB.Administrator)
{
	HUB.Administrator = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Administrator.Newsletter = {
	
	jQuery: jq,
	
	initialize: function()
	{
		var $ = this.jQuery,
			scheduler = $('#scheduler');

		$('#add-newsletter').on('click', function (e){
			e.preventDefault();

			Joomla.submitbutton('add');
		});

		$('#btn-manage').on('click', function (e){
			e.preventDefault();

			Joomla.submitbutton('manage');
		});

		//if we have a scheduler
		if (scheduler.length)
		{
			//hide the pickers
			$('#scheduler-alt').hide();
			
			//attach event to show pickers if we change to schedule specific time
			$('input[name=scheduler]').on('change', function(event){
				if ($(this).val() == 0)
				{
					$('#scheduler-alt').show();
				}
				else
				{
					$('#scheduler-alt').hide();
				}
			});
			
			//date picker for scheduler
			$('input[name=scheduler_date]').datepicker({
				showAnim: "slideDown",
				minDate: 0
			});
		}

		// stop page refresh if form has changed except on submit
		if (jQuery().formWatcher)
		{
			$('form').formWatcher();
		}
		
		HUB.Administrator.Newsletter.locationMap();
		HUB.Administrator.Newsletter.mailingListAndCount();
	},
	
	sendNewsletterCheck: function()
	{
		var $ = this.jQuery,
			scheduler = $("input[name=scheduler]:checked"),
			mailinglist = $("#mailinglist");
		
		//make sure we have set a schedule
		if (scheduler.val() == '0')
		{
			var scheduler_date = $("#scheduler_date").val(),
				scheduler_date_hour = $("#scheduler_date_hour").val(),
				scheduler_date_minute = $("#scheduler_date_minute").val(),
				scheduler_date_meridian = $("#scheduler_date_meridian").val();
			
			//make sure we have filled out the sceduled date and time
			if(scheduler_date == '' || scheduler_date_hour == '' || scheduler_date_minute == '' || scheduler_date_meridian == '')
			{
				alert('You must fill out all the newsletter scheduling fields.')
				return false;
			}
		}
		
		//make sure we have a mailing list
		if (mailinglist.val() == '' || mailinglist.val() == 0)
		{
			alert("You must select a mailing list to send the newsletter to.");
			mailinglist.focus();
			return false;
		}
		
		return true;
	},
	
	sendNewsletterDoubleCheck: function()
	{
		var $ = this.jQuery,
			message = '',
			message_datetime = '',
			scheduler = $("input[name=scheduler]:checked"),
			newsletterName = $("#newsletter-name").val(),
			scheduler_date = $("#scheduler_date").val(),
			scheduler_date_hour = $("#scheduler_date_hour").val(),
			scheduler_date_minute = $("#scheduler_date_minute").val(),
			scheduler_date_meridian = $("#scheduler_date_meridian").val();
			
		if (scheduler.val() == 1)
		{
			message_datetime = 'Now (Your email might take up to one hour to send)';
		}
		else
		{
			message_datetime = scheduler_date + ' at ' + scheduler_date_hour + ':' + scheduler_date_minute + ' ' + scheduler_date_meridian
		}
		
			
		//create message to confirm user wants to perform task
		message  = "Are you sure you want to send the following newsletter? \n\n";
		message += newsletterName.replace(/\s+/g, ' ');
		message += "\n\n---------- On ----------\n\n";
		message += message_datetime;
			
		//output confirm box
		if (!confirm( message ))
		{
			return false;
		}
		
		return true;
	},
	
	newsletterPreview: function( id )
	{
		var $ = this.jQuery;
		
		$.fancybox({
			type: 'iframe',
			href: 'index.php?option=com_newsletter&task=preview&id=' + id + '&tmpl=component',
			scrollOutside: false
		})
	},

	mailingListAndCount: function()
	{
		var $ = this.jQuery;

		$('#mailinglist').on('change', function(event) {
			var value = $(this).val();
			if (value != '' && value != 0)
			{
				$.ajax({
					type: 'get',
					dataType: 'json',
					url: 'index.php?option=com_newsletter&controller=mailinglist&task=emailcount&mailinglistid='+value+'&no_html=1',
					success: function(data)
					{
						var emailCount = data.length;

						//show count
						$('#mailinglist-count').show();

						//set actual counter
						$('#mailinglist-count').find('#mailinglist-count-count').html(emailCount);

						//add list of emails
						$('#mailinglist-emails').html('<br />--------------------------------<br />' + data.join('<br />'));
					}
				});
			}
			else
			{
				$('#mailinglist-count').hide();
			}
		});

		$('#mailinglist-count').hide();
	},
	
	locationMap: function() 
	{
		var $ = this.jQuery;
		
		if ($("#location-map-container").length)
		{
			//lets hide the us map for now
			$('#us-map').hide();
			
			var worldData = JSON.parse( $('#world-map-data').attr('data-src') );
			var usData = JSON.parse( $('#us-map-data').attr('data-src') );
			
			//add World map
			$('#world-map').vectorMap({
				map: 'world_mill_en',
				backgroundColor: 'transparent',
				regionStyle: {
					initial: {
						'fill': '#CCCCCC',
						'fill-opacity': 1,
						'stroke': '#FFFFFF',
						'stroke-width': 1,
						'stroke-opacity': 1
					},
					hover: {
						'fill': '#BBBBBB',
						'fill-opacity': 1,
						'stroke': 'none',
						'stroke-width': 0,
						'stroke-opacity': 1
					}
				},
				series: {
					regions: [{
						values: worldData,
						scale: ['#607581', '#414f57'],
						normalizeFunction: 'polynomial'
					}]
				},
				onRegionClick: function(event, region) {
					var worldMapObject = $('#world-map').vectorMap('get', 'mapObject');
					
					if (region == 'US')
					{
						//hide world map to show states
						$('#world-map').hide();
						$('#us-map').show();
						$('.jvectormap-world').show();
						
						//set us map size before show
						var usMapObject = $('#us-map').vectorMap('get', 'mapObject');
						usMapObject.setSize();
					}
					else
					{
						worldMapObject.setFocus( region );
					}
				},
				onRegionLabelShow: function(e, el, code) {
					var html,
						country = el.html(),
						count = (worldData[code]) ? worldData[code] : 0;
					
					//build html for label tooltip
					html = '<div class="title">' + country + '</div><div class="count"> ' + count + ' newsletter opens</div>';
					el.html( html );
				}
			});
			
			//add US map
			$('#us-map').vectorMap({
				map: 'us_mill_en',
				backgroundColor: 'transparent',
				regionStyle: {
					initial: {
						'fill': '#CCCCCC',
						'fill-opacity': 1,
						'stroke': '#FFFFFF',
						'stroke-width': 1,
						'stroke-opacity': 1
					},
					hover: {
						'fill': '#BBBBBB',
						'fill-opacity': 1,
						'stroke': 'none',
						'stroke-width': 0,
						'stroke-opacity': 1
					}
				},
				series: {
					regions: [{
						values: usData,
						scale: ['#607581', '#414f57'],
						normalizeFunction: 'polynomial'
					}]
				},
				onRegionClick: function(event, region) {
					//focus on area when we click
					var mapObject = $('#us-map').vectorMap('get', 'mapObject');
					mapObject.setFocus( region );
				},
				onRegionLabelShow: function(e, el, code) {
					var html,
						country = el.html(),
						count = (usData[code]) ? usData[code] : 0;
					
					//build html for label tooltip
					html = '<div class="title">' + country + '</div><div class="count"> ' + count + ' newsletter opens</div>';
					el.html( html );
				}
			});
			
			//add click event to world map button
			$('.jvectormap-world').on('click', function(event) {
				$('#us-map').hide();
				$('#world-map').show();
				$('.jvectormap-world').hide();
				var worldMapObject = $('#world-map').vectorMap('get', 'mapObject');
				worldMapObject.setSize();
			});
		}
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Administrator.Newsletter.initialize();

	// get iframe and mozified code
	var previewIframe = $('#preview-iframe');

	if (previewIframe.length) {
		previewCode = $('#preview-code').find('table').first();

		// set iframe height and width
		// add preview code to iframe
		previewIframe
			.css({
				width: previewCode.attr('width') + 'px',
				height: previewCode.attr('height') + 'px'
			})
			.contents().find('html').html(previewCode);
	}
});
