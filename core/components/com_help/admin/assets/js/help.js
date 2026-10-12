/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

window.onload = function() {
	var history = window.history,
		back    = document.getElementById('back');

	// Not every help page has a back link; click() would fire a click rather
	// than attach a handler, so listen for clicks instead
	if (back === null) {
		return;
	}

	back.addEventListener('click', function(e){
		e.preventDefault();
		window.history.back();
	});

	if (history.length > 1) {
		back.style.display = "block";
	}
};
