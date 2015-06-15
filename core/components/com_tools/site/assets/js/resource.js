/**
 * @package     hubzero-cms
 * @file        components/com_contribtool/contribtool.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

		$('.returntoedit').each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();

				var editform = document.getElementById("hubForm");
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
