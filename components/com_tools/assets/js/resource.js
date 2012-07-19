/**
 * @package     hubzero-cms
 * @file        components/com_contribtool/contribtool.js
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
// Contribtool admin actions form
//----------------------------------------------------------
HUB.ToolsResource = {
	initialize: function() {
		var editform = document.getElementById("hubForm");
		if(editform) {
			$$('.returntoedit').each(function(item) {
			item.addEvent('click', function() {
			var editform = document.getElementById("hubForm");
			editform.step.value = editform.step.value-2;
			editform.task.value = "start";
			editform.submit( );
			return false;
			}
				);
			});
		}
	},

	hide: function(obj) {
		$(obj).style.display = 'none';
	},
	
	show: function(obj) {
		$(obj).style.display = 'block';
	}
}

window.addEvent('domready', HUB.ToolsResource.initialize);
