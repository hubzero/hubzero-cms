/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

//----------------------------------------------------------
// Trouble Report form
//----------------------------------------------------------
HUB.Modules.ReportProblems = {

	initialize: function(trigger) {
		var pane = $('#help-pane'),
			trigger = $(trigger);

		if (!pane.length || !trigger.length) {
			return;
		}

		trigger.fancybox({
			type: 'iframe',
			href: pane.attr('data-form'),
			width: 700,
			//height: 400,
			autoSize: false,
			fitToView: true,
			titleShow: false,
			arrows: false,
			closeBtn: true,
			afterLoad: function() {
				// Nothing to do here.
			}
		});
	}
};

/*jQuery(document).ready(function(jq) {
	HUB.Modules.ReportProblems.initialize('#tab');
});*/

