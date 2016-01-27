/**
 * @package     hubzero-cms
 * @file        components/com_time/time.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Time = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Set a few variables
		var dc     = $("#dialog-confirm");

		// Confirm starts out false so that we know to prevent default action of delete link until 'delete' button is pushed
		var confirm = false;

		// Add click event to delete buttons
		$(".delete").click(function(event) {
			if(confirm === false) {
				// Prevent delete action
				event.preventDefault();

				// This is the confirm dialog box message
				var msg = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This entry will be permanently deleted and cannot be recovered. Are you sure?</p>';
				// Also grab the url that was being loaded (i.e. the item that is to be deleted)
				var action = $(this).attr("href");

				// Set dialog box message and title
				dc.html(msg);
				dc.attr('title','Delete Entry?');

				// Create the dialog box
				dc.dialog({
					resizable: false,
					height: 180,
					width: 350,
					modal: true,
					buttons: {
						Cancel: function() {
							// Close the dialog box, as if nothing happened
							$(this).dialog("close");
						},
						"Delete entry": function() {
							// Follow the delete link again, this time with confirm true so the action goes through as expected
							confirm = true;
							window.location.href = action;
						}
					}
				});
			}
		});

		// Date picker for date input field
		$(".hadDatepicker").datepicker({
			// Set a unix/MySQL friendly date format
			dateFormat: 'yy-mm-dd'
		});

		if (!!$.prototype.select2) {
			$('select').not('.no-search').select2({
				placeholder : "search...",
				width       : "100%"
			});

			$('select.no-search').select2({
				minimumResultsForSearch: -1,
				width       : "100%"
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.Time.initialize();
});