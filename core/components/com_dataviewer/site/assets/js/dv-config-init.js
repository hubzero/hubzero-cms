/**
 * Dataviewer config initializer.
 *
 * Reads configuration from data-* attributes on #dv-config
 * and sets the global variables expected by the legacy JS.
 *
 * Must be loaded BEFORE spreadsheet.js, dv-spreadsheet-charts.js,
 * and custom-views.js so this DOMContentLoaded handler fires first.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var el = document.getElementById('dv-config');
	if (!el) {
		return;
	}

	// Parse data and settings from data attributes
	try {
		window.dv_data = JSON.parse(el.getAttribute('data-dv-data'));
	} catch (e) {
		window.dv_data = {};
	}

	try {
		window.dv_settings = JSON.parse(el.getAttribute('data-dv-settings'));
	} catch (e) {
		window.dv_settings = {};
	}

	// Additional flags
	window.dv_settings.show_charts = parseInt(el.getAttribute('data-dv-show-chart'), 10) || 0;
	window.dv_settings.show_customizer = el.getAttribute('data-dv-show-customizer') === 'true';
	window.dv_settings.show_filters = el.getAttribute('data-dv-show-filters') === 'true';
	window.dv_settings.show_map = el.getAttribute('data-dv-show-map') || '';
});
