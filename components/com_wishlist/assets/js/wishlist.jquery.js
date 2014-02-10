/**
 * @package     hubzero-cms
 * @file        components/com_wishlist/wishlist.js
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
// Answers Scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Wishlist = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
				
		// due date
		if ($('#nodue').length > 0) { 
			var frm = $('#hubForm');
			$('#nodue').on('click', function() {
				frm.publish_up.val('');
			});
		}
		
		if ($('#publish_up').length > 0) {
			$( "#publish_up" ).datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				maxDate: '+10Y'
			});
		}
		
		// show/hide plan area
		if ($('#section-plan').length && $('#part_plan').length) { 
			$('#part_plan').on('click', function() {
				if ($('#part_plan').hasClass('collapse')) {
					$('#part_plan').removeClass('collapse');
					$('#full_plan').css('display', "none");
					$('#part_plan').addClass('expand');
				} else {
					$('#part_plan').removeClass('expand');
					$('#full_plan').css('display', "block");
					$('#part_plan').addClass('collapse');
				}
				return false;
			});
		}
	},

	setZindex: function(el) {
		var LIs = el.parentNode.parentNode.parentNode.getElementsByTagName('li');

		if (LIs) {
			for (i = 0; i < LIs.length; i++) {
				LIs[i].style.zIndex = 1;
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.Wishlist.initialize();
});
