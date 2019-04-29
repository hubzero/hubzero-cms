/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq;

	$('span.asset-primary').on('click', function(){
		var el = $($(this).parent());
		if (el.hasClass('collapsed')) {
			el.removeClass('collapsed');
		} else {
			el.addClass('collapsed');
		}
	});

	$('.unit-content h3').on('click', function(){
		var el = $($(this).parent());
		if (el.hasClass('collapsed')) {
			el.removeClass('collapsed');
		} else {
			el.addClass('collapsed');
		}
	});

	$('.advertise-popup').fancybox({
		type: 'iframe',
		height:($(window).height())*5/6,
		autoSize: false
	});

	// Enable local storage of open/closed state of units
	if (window.indexedDB) {
		// Database connection
		var db,
			request = indexedDB.open('courses', 1),
			scope   = 'outline.unit';

		request.onupgradeneeded = function (e) {
			db = e.target.result;

			// Create a store to hold the data
			db.createObjectStore(scope, { keyPath: 'id' });
		};

		request.onsuccess = function (e) {
			db = e.target.result;

			// Loop through and set state if we already have it
			$('.unit-content').each(function (i, v) {
				var unit         = $(this),
					transaction  = db.transaction([ scope ], 'readonly'),
					objectStore  = transaction.objectStore(scope),
					object       = objectStore.get(unit.data('id'));

				object.onsuccess = function (e) {
					if (e.target.result && e.target.result.collapsed) {
						unit.addClass('collapsed');
					}
				};
			});
		};

		// Update info
		$('.unit-content h3').on('click', function () {
			// Create the transaction
			var transaction = db.transaction([ scope ], 'readwrite'),
				unit    = $(this).parents('.unit-content'),
				data    = {
					collapsed : unit.hasClass('collapsed'),
					id        : unit.data('id')
				};

			var request = transaction.objectStore(scope).put(data);
		});
	}
});