/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Publication review page JS
//----------------------------------------------------------
HUB.ProjectPublicationReview = {
	contactEmailCheck: function()
	{	
		$("input[name='contact[]']").change(function() {
			var emailID = '#' + $(this).val() + '_email';
			var msgID = '#' + $(this).val() + '_msg';
			
			if ($(this).prop('checked'))
			{
				if ($(this).parents().has(emailID).length == 0)
				{
					$(msgID).show();
				}
			}
			else
			{
				if ($(this).parents().has(emailID).length == 0)
				{
					$(msgID).hide();
				}
			}
		});
	}
}

jQuery(document).ready(function($){
	HUB.ProjectPublicationReview.contactEmailCheck();
});	
