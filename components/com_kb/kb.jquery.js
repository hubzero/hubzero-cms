/**
 * @package     hubzero-cms
 * @file        components/com_kb/kb.jquery.js
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
// Registration form validation
//----------------------------------------------------------
HUB.Kb = {
	
	initialize: function() {
		// Voting
		$('.vote-link').each(function(i, el) {
			$(el).click(function (e) {
				if ($(this).attr('href')) {
					e.preventDefault();
					$.get($(this).attr('href')+'?no_html=1', {}, function(data) {
		            	$($(el).parent().parent()).html(data);
					});
					return false;
				}
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.Kb.initialize();
});


