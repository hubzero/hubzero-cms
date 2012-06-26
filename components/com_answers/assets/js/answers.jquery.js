/**
 * @package     hubzero-cms
 * @file        components/com_answers/answers.js
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

HUB.Answers = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
			
		$('.reply').each(function(i, item) {
			$(item).click(function (e) {
				e.preventDefault();
				var cfrm = $('#' + $(this).attr('rel'));
				if (cfrm.hasClass('hide')) {
					cfrm.removeClass('hide');
				} else {
					cfrm.addClass('hide');
				}
			});
		});
		
		$('.cancelreply').each(function(i, item) {
			$(item).click(function (e) {
				e.preventDefault();
				$(this).closest('.addcomment').addClass('hide');
			});
		});
	}
	
}

jQuery(document).ready(function($){
	HUB.Answers.initialize();
});
