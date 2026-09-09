/**
 * Spreadsheet initialization script.
 *
 * Reads configuration from data attributes on #dv-config element
 * instead of inline <script> globals, for CSP compliance.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

(function () {
	'use strict';

	var configEl = document.getElementById('dv-config');
	if (!configEl) {
		return;
	}

	// Parse configuration from data attributes
	window.dv_data = JSON.parse(configEl.getAttribute('data-dv-data'));
	window.dv_settings = JSON.parse(configEl.getAttribute('data-dv-settings'));
	window.dv_show_filters = configEl.getAttribute('data-dv-show-filters') === 'true';

	var showChart = configEl.getAttribute('data-dv-show-chart');
	window.dv_settings.show_charts = showChart && showChart !== '0'
		? parseInt(showChart, 10)
		: undefined;

	window.dv_show_customizer = configEl.getAttribute('data-dv-show-customizer') === 'true';

	var showMap = configEl.getAttribute('data-dv-show-map');
	window.dv_show_maps = showMap && showMap !== '' ? showMap : undefined;
})();
