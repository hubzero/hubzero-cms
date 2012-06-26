/**
 * @package     hubzero-cms
 * @file        components/com_events/events.jquery.js
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
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Events = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		$('#publish_up').datepicker({
			dateFormat: "yy-mm-dd"
		});
		$('#publish_down').datepicker({
			dateFormat: "yy-mm-dd"
		});
	}
}

jQuery(document).ready(function($){
	HUB.Events.initialize();
});