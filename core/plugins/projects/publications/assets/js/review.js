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
	contactCheck: function ()
	{
		$("input[name='contact[]']").change(function() {
			var emailID = '#' + $(this).val() + '_email';
			var deptID = '#' + $(this).val() + '_dept';
			var orgID = '#' + $(this).val() + '_org';
			var msgID = '#' + $(this).val() + '_missing_item';
			
			if ($(this).prop('checked'))
			{
				if (($(this).parents().has(emailID).length == 0) || ($(this).parents().has(deptID).length == 0) || ($(this).parents().has(orgID).length == 0))
				{
					$(msgID).show();
				}
			}
			else
			{
				$(msgID).hide();
			}
		});
	}
}

jQuery(document).ready(function($){
	HUB.ProjectPublicationReview.contactCheck();
});	
