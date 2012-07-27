/**
 * @package     hubzero-cms
 * @file        plugins/time/tasks/tasks.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}
if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Time Tasks
//----------------------------------------------------------
HUB.Plugins.TimeTasks = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Set variables
		var hub      = $(".filters-inner select#hub_id");
		var priority = $(".filters-inner select#priority");
		var assignee = $(".filters-inner select#assignee");
		var liaison  = $(".filters-inner select#liaison");

		// Add change event to hub select box (filter list of tasks by hub)
		hub.change(function(event) {
			window.location.href = '/time/tasks?hub='+$(this).val();
		});

		// Add change event to priority select box (filter list of tasks by priority)
		priority.change(function(event) {
			window.location.href = '/time/tasks?priority='+$(this).val();
		});

		// Add change event to assignee select box (filter list of tasks by assignee)
		assignee.change(function(event) {
			window.location.href = '/time/tasks?aname='+$(this).val();
		});

		// Add change event to liaison select box (filter list of tasks by liaison)
		liaison.change(function(event) {
			window.location.href = '/time/tasks?lname='+$(this).val();
		});
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.TimeTasks.initialize();
});