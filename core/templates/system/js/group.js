/**
 * @package     hubzero-cms
 * @file        templates/system/js/group.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// group pane toggle
	$('#group a.toggle, #group-info .close').on('click', function(event) {
		event.preventDefault();
		
		$('#group-info').slideToggle('normal');
		$('#group-body').toggleClass('opened');
	});

	// make sure group pane isnt open at the same time as help
	$('#tab').on('click', function(event) {
		if ($('#group-body').hasClass('opened'))
		{
			$('#group-info').slideToggle('normal');
			$('#group-body').toggleClass('opened');
		}
	});
});