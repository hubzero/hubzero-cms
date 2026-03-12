/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	setTimeout(doWork, 10);

	function doWork() {
		var rows = document.querySelectorAll('.quota-row');

		for (var i = 0; i < rows.length; i++) {
			(function(el) {
				var idInput = el.querySelector('.row-id');
				var usage = el.querySelector('.usage-outer');
				var id = idInput ? idInput.value : '';
				var url = el.getAttribute('data-quota');

				fetch(url + '?' + new URLSearchParams({ id: id }).toString(), {
					method: 'GET',
					headers: { 'Accept': 'application/json' }
				})
				.then(function(response) { return response.json(); })
				.then(function(data) {
					if (data.percent > 100) {
						data.percent = 100;
						var inner = usage.querySelector('.usage-inner');
						if (inner) { inner.classList.add('max'); }
					}
					var calc = usage.previousElementSibling;
					if (calc && calc.classList.contains('usage-calculating')) {
						calc.style.display = 'none';
					}
					usage.style.display = '';
					usage.style.opacity = '0';
					// Fade in
					var op = 0;
					var fadeTimer = setInterval(function() {
						op += 0.05;
						if (op >= 1) {
							op = 1;
							clearInterval(fadeTimer);
						}
						usage.style.opacity = op;
					}, 20);
					var inner = usage.querySelector('.usage-inner');
					if (inner) { inner.style.width = data.percent + '%'; }
				})
				.catch(function() {
					var calc = usage.previousElementSibling;
					if (calc && calc.classList.contains('usage-calculating')) {
						calc.style.display = 'none';
					}
					var unavail = usage.nextElementSibling;
					if (unavail && unavail.classList.contains('usage-unavailable')) {
						unavail.style.display = '';
					}
				});
			})(rows[i]);
		}
	}
});
