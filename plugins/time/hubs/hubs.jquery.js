/**
 * @package     hubzero-cms
 * @file        plugins/time/hubs/hubs.js
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

		// Define a few variables
		var focus_text = '';
		var n_changed  = false;
		var p_changed  = false;
		var e_changed  = false;
		var r_changed  = false;

		// Remove the placeholder text from the contact fields when focus in on field
		$(".new_contact").focusin(function(){

			// Save the previous value before clearing the field in case we want to use it later
			var focus_text = $(this).val();

			// Only clear the field if the text is the placeholder text
			if(focus_text == 'name' || focus_text == 'phone' || focus_text == 'email' || focus_text == 'role'){
				$(this).val('');
			}
		});

		// When focus leaves the field, do...
		$(".new_contact").focusout(function(){
			// Add the placeholder text back if nothing was added to the field
			if($(this).val() == '') {
				$(this).val(focus_text);
			}

			// Darken the text color and decrease the opacity when a value is entered
			if($(this).val() != 'name' && $(this).val() != 'phone' && $(this).val() != 'email' && $(this).val() != 'role'){
				$(this).css('color', 'initial');
				$(this).css('opacity', 1);
			}

			// Set variables tracking - whether text has been provided for a given field
			if($("#new_name").val() != 'name'){
				var n_changed = true;
				$("#new_name").removeClass('contact_error');
			} else if($("#new_name").val() == 'name'){
				$("#new_name").addClass('contact_error');
			}
			if($("#new_phone").val() != 'phone'){
				var p_changed = true;
				$("#new_phone").removeClass('contact_error');
			} else if($("#new_phone").val() == 'phone'){
				$("#new_phone").addClass('contact_error');
			}
			if($("#new_email").val() != 'email'){
				var e_changed = true;
				$("#new_email").removeClass('contact_error');
			} else if($("#new_email").val() == 'email'){
				$("#new_email").addClass('contact_error');
			}
			if($("#new_role").val() != 'role'){
				var r_changed = true;
				$("#new_role").removeClass('contact_error');
			} else if($("#new_role").val() == 'role'){
				$("#new_role").addClass('contact_error');
			}

			// If all fields have been filled in, add a button for saving the current contact and providing a new row
			if(n_changed && p_changed && e_changed && r_changed){
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
			if(confirm == false) {
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
					var new_row  = '<div class="grouping contact-grouping" id="brand_new">';
						new_row += '<a href="/time/hubs/deletecontact/'+cid+'" class="delete_contact" title="Delete contact"></a>';
						new_row += '<input type="text" name="contact['+cid+'][name]" value="'+name+'" />';
						new_row += '<input type="text" name="contact['+cid+'][phone]" value="'+phone+'" />';
						new_row += '<input type="text" name="contact['+cid+'][email]" value="'+email+'" />';
						new_row += '<input type="text" name="contact['+cid+'][role]" value="'+role+'" />';
						new_row += '<input type="hidden" name="contact['+cid+'][id]" value="'+cid+'" />';
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
}

jQuery(document).ready(function($){
	HUB.Plugins.TimeHubs.initialize();
});