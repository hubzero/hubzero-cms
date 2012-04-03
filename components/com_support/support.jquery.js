/**
 * @package     hubzero-cms
 * @file        components/com_support/support.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Support
//----------------------------------------------------------

HUB.Support = {
	getMessage: function() {
		var id = $('#messages');
		if (id.val() != 'mc') {
			var hi = $('#'+id.val()).val();
			var co = $('#comment');
			co.val(hi);
		} else {
			var co = $('#comment');
			co.val('');
		}
	},
	
	initialize: function() {
		$('.fixedImgTip').each(function(i, el) {
			$(this).removeAttr('title');
			$(this).removeAttr('href'); // Firefox seems to fail if there's an href
		});
		$('.fixedImgTip').tooltip({
			offset: [160, 2]
		});
			
		if ($('#messages')) {
			$('#messages').bind('change', HUB.Support.getMessage);
		}
		
		if ($('#toggle-details')) {
			$('#toggle-details').click(function() {
				var tbody = $('#ticket-details-body');
				if (tbody.hasClass('hide')) {
					tbody.removeClass('hide');
				} else {
					tbody.addClass('hide');
				}
				return false;
			});
		}
		
		if ($('#make-private')) {
			$('#make-private').click(function() {
				var es = $('#email_submitter');
				if ($('#make-private').attr('checked')) {
					if ($('#email_submitter').attr('checked')) {
						$('#email_submitter').removeAttr('checked');
						$('#email_submitter').attr('disabled', 'disabled');
					}
					$('#commentform').addClass('private');
				} else {
					$('#email_submitter').attr('checked', 'checked');
					$('#email_submitter').removeAttr('disabled');
					$('#commentform').removeClass('private');
				}
			});
		}
	}
}

jQuery(document).ready(function($){
	HUB.Support.initialize();
});
