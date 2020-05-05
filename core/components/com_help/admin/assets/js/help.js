/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

window.onload = function() {
	var history = window.history,
		back    = document.getElementById('back');

	back.click(function(e){
		window.history.back();
	});

	if (history.length > 1 && back !== null) {
		back.style.display = "block";
	}
};
