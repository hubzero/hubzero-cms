/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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