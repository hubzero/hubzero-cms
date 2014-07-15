/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/hubs.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}
if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Time Hubs
//----------------------------------------------------------
HUB.Plugins.TimeHubs = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// --------------------
		// Contact fields
		// --------------------

		// When focus leaves the field, do...
		$(".new_contact").focusout(function() {
			// Darken the text color and decrease the opacity when a value is entered
			if ($(this).val()) {
				$(this).css('color', 'initial');
				$(this).css('opacity', 1);
			}

			// If all fields have been filled in, add a button for saving the current contact and providing a new row
			if ($("#new_name").val() && $("#new_phone").val() && $("#new_email").val() && $("#new_role").val()) {
				$('#save_new_contact').css('visibility', 'visible');
			}
		});

		// --------------------
		// Delete confirmation
		// --------------------

		// Set a variable for our dialog div
		var dc = $("#dialog-confirm");

		// Confirm starts out false so that we know to prevent default action of delete link until 'delete' button is pushed
		var confirm = false;

		// Add click event to delete buttons
		$("body").on("click", ".delete_contact", function(event) {
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

		// --------------------
		// Save contact
		// --------------------

		var sc = $(".save_contact");

		// Add click event to save contact button
		sc.click(function(event) {

			// Prevent delete action
			event.preventDefault();

			// @FIXME: maybe add some checks here

			// Grab the variables
			var name  = $("#new_name").val();
			var phone = $("#new_phone").val();
			var email = $("#new_email").val();
			var role  = $("#new_role").val();
			var hid   = $("#hub_id").val();

			// Create a ajax call to save the contact
			$.ajax({
				url: "/api/time/saveContact",
				data: "hid="+hid+"&name="+name+"&phone="+phone+"&email="+email+"&role="+role,
				dataType: "json",
				cache: false,
				success: function(json){
					// If success, add another row for a new contact

					// Get the new contact id
					var cid = json;

					// Create the new row
					var new_row  = '<div class="grouping contact-grouping grid" id="brand_new">';
						new_row += '<div class="col span4">';
						new_row += '<input type="text" name="contact['+cid+'][name]" placeholder="'+name+'" />';
						new_row += '</div>';
						new_row += '<div class="col span2">';
						new_row += '<input type="text" name="contact['+cid+'][phone]" placeholder="'+phone+'" />';
						new_row += '</div>';
						new_row += '<div class="col span2">';
						new_row += '<input type="text" name="contact['+cid+'][email]" placeholder="'+email+'" />';
						new_row += '</div>';
						new_row += '<div class="col span2">';
						new_row += '<input type="text" name="contact['+cid+'][role]" placeholder="'+role+'" />';
						new_row += '</div>';
						new_row += '<input type="hidden" name="contact['+cid+'][id]" value="'+cid+'" />';
						new_row += '<div class="col span2 omega">';
						new_row += '<a href="/time/hubs/deletecontact/'+cid+'" class="btn btn-danger icon-delete delete_contact" title="Delete contact">Delete</a>';
						new_row += '</div>';
						new_row += '</div>';

					// Clear the values from the new contact fields and reset styles, now that we saved
					$("#new_name").val('name');
					$("#new_phone").val('phone');
					$("#new_email").val('email');
					$("#new_role").val('role');
					$(".new_contact").removeAttr('style');
					$(".new_contact_row input").removeAttr('style');
					$(".save_contact").removeAttr('style');

					// Add the new row and fade it in
					$('#new-contact-group').before(new_row);
					$('#brand_new').slideDown('slow', 'linear', function() {
						// Animation complete
						var new_id = 'contact-' + cid + '-group';
						$('#brand_new').attr('id', new_id);
					});
				}
			});
		});
	} // end initialize
};

jQuery(document).ready(function($){
	HUB.Plugins.TimeHubs.initialize();
});