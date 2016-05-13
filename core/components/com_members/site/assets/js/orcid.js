/**
 * @package     hubzero-cms
 * @file        components/com_register/site/assets/js/orcid.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

HUB.Orcid = {
	fetchOrcidRecords: function() {
		var $ = jq;

		var firstName = $('#first-name').val();
		var lastName = $('#last-name').val();
		var email = $('#email').val();

		if (!firstName && !lastName && !email) {
			alert('Please fill at least one of the fields.');
			return;
		}

		// return param: 1 means return ORCID to use to finish registration, assumes registration page
		// return param: 0 means do not return ORCID, assumes profile page
		$.ajax({
			url: $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=fetch&no_html=1&fname=' + firstName + '&lname=' + lastName + '&email=' + email + '&return=1',
			type: 'GET',
			success: function(data, status, jqXHR) {
				$('#section-orcid-results').html(jQuery.parseJSON(data));
			}
		});
	},

	fetchOrcid: function() {
		var $ = jq;

		$('body').on('click', '#get-orcid-results', function(event) {
			event.preventDefault();

			HUB.Orcid.fetchOrcidRecords();
		});
	},

	associateOrcid: function(parentField, orcid) {
		window.parent.document.getElementById('profile_orcid').value = orcid;
		window.parent.jQuery.fancybox.close();
	},

	createOrcid: function(fname, lname, email) {
		var $ = jq,
			uri = $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=create&no_html=1&fname=' + fname + '&lname=' + lname + '&email=' + email;

		$.ajax({
			url: uri,
			type: 'GET',
			success: function(data, status, jqXHR) {
				var response = jQuery.parseJSON(data);

				if (response.success) {
					if (response.orcid) {
						alert('Successful creation of your new ORCID. Claim the ORCID through the link sent to your email.');
						window.parent.document.getElementById('profile_orcid').value = response.orcid;
						window.parent.jQuery.fancybox.close();
					} else {
						alert('ORCID service reported a successful creation but we failed to retrieve an ORCID. Please contact support.');
					}
				} else {
					if (response.message) {
						alert(response.message);
					} else {
						alert('Failed to create a new ORCID. Possible existence of an ORCID with the same email.');
					}
				}
			}
		});
	}
}

jQuery(document).ready(function($){
	if ($('.orcid-fetch').length > 0) {
		$('.orcid-fetch').on('click', function(e) {
			e.preventDefault();

			$.fancybox({
				type: 'iframe',   // change this to 'ajax' if you want to use AJAX
				width: 700,
				height: 'auto',
				autoSize: false,
				fitToView: false,
				titleShow: false,
				closeClick: false,
				helpers: { 
					overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
				},
				tpl: {
					wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
				},
				beforeLoad: function() {
					href = $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&return=1&fname=' + $('#first-name').val() + '&lname=' + $('#last-name').val()  + '&email=' + $('#email').val();
					if (href.indexOf('?') == -1) {
						href += '?tmpl=component';    // Change to no_html=1 if using AJAX
					} else {
						href += '&tmpl=component';    // Change to no_html=1 if using AJAX
					}
					$(this).attr('href', href);
				}
			});
		});
	}

	HUB.Orcid.fetchOrcid();
});
