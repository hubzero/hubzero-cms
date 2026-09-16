/**
 * Mass Mail -- review / verification page.
 */
(function () {
	document.addEventListener('DOMContentLoaded', function () {
		// HTML / plain-text preview toggle.
		var bHtml = document.getElementById('mm-pv-btn-html');
		var bText = document.getElementById('mm-pv-btn-text');
		var pHtml = document.getElementById('mm-pv-html');
		var pText = document.getElementById('mm-pv-text');
		function show(isHtml) {
			if (pHtml) { pHtml.hidden = !isHtml; }
			if (pText) { pText.hidden = isHtml; }
			if (bHtml) { bHtml.classList.toggle('is-active', isHtml); }
			if (bText) { bText.classList.toggle('is-active', !isHtml); }
		}
		if (bHtml) { bHtml.addEventListener('click', function () { show(true); }); }
		if (bText) { bText.addEventListener('click', function () { show(false); }); }

		// Submit buttons (Accept -> launch).
		var submits = document.querySelectorAll('.mm-submit');
		for (var i = 0; i < submits.length; i++) {
			submits[i].addEventListener('click', function () {
				if (window.Joomla && Joomla.submitbutton) { Joomla.submitbutton(this.getAttribute('data-task')); }
			});
		}

		// Confirm destructive links (Discard).
		var confirmers = document.querySelectorAll('.mm-confirm');
		for (var c = 0; c < confirmers.length; c++) {
			confirmers[c].addEventListener('click', function (e) {
				var msg = this.getAttribute('data-confirm');
				if (msg && !window.confirm(msg)) { e.preventDefault(); }
			});
		}
	});
})();
