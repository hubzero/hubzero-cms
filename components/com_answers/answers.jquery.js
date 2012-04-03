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
HUB.Answers = {

	initialize: function() {
		var com = this,
			$ = this.jQuery,
			settings = this.settings;
			
		$('.showreplyform').each(function(i, item) {
			$(item).click(function () {
				$(this).closest('.addcomment').show();
			});
		});
		
		$('.closeform').each(function(i, item) {				 
			$(item).click(function () {
				$(this).closest('.addcomment').hide();
			});
		});
	}
	
}

jQuery(document).ready(function($){
	HUB.Answers.initialize();
});
