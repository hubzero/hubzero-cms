/**
 * @package     hubzero-cms
 * @file        modules/mod_newsletter/mod_newsletter.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */


//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {
		Modules: {}
	};
} else if (!HUB.Modules) {
	HUB.Modules = {};
}

if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// My Sessions Module
//----------------------------------------------------------
HUB.Modules.Newsletter = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		$('#sign-up-submit').on('click', function(event) {
			var email = $(this).parents('form').find('#email'),
				filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				
			//check to make sure we have an email address and its valid
			if (email.val() == '' || !filter.test(email.val()))
			{
				event.preventDefault();
				email.focus();
				alert('In order to sign up, you must enter a valid email address.')
			}
		});
	}
};

jQuery(document).ready(function($){
	HUB.Modules.Newsletter.initialize();
});