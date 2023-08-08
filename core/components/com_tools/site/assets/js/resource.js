/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Contribtool admin actions form
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ToolsResource = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Previous Button on the Tool Edit Description Wizard
		$('.returntoedit').each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();

				var editform = document.getElementById("hubForm");
				editform.controller.value = "resources";
				editform.step.value = editform.step.value-2;
				editform.task.value = "start";
				editform.submit();
				return false;
			});
		});
	},

	hide: function(obj) {
		$(obj).css('display', 'none');
	},
	
	show: function(obj) {
		$(obj).css('display', 'block');
	}
}

jQuery(document).ready(function($){
	HUB.ToolsResource.initialize();
});
