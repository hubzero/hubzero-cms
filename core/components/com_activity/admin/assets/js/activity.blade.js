/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	document.querySelectorAll('.com_activity-chart').forEach(function (el) {
		var dataEl = document.getElementById(el.getAttribute('data-datasets'));
		if (!dataEl) {
			return;
		}
		var datasets = JSON.parse(dataEl.content.textContent);

		jQuery.plot(jQuery(el), datasets.datasets, {
			series: {
				lines: {
					show: true,
					fill: false
				},
				points: { show: false },
				shadowSize: 0
			},
			grid: {
				color: 'rgba(0, 0, 0, 0.6)',
				borderWidth: 1,
				borderColor: 'transparent',
				hoverable: true,
				clickable: true
			},
			tooltip: true,
			tooltipOpts: {
				content: "%y %s in %x",
				shifts: {
					x: -60,
					y: 25
				},
				defaultTheme: false
			},
			legend: {
				show: false
			},
			xaxis: {
				mode: "time",
				tickDecimals: 0,
				tickFormatter: function (val) {
					var d = new Date(val);
					return monthShort[d.getUTCMonth()] + ' ' + d.getUTCDate();
				}
			},
			yaxis: {
				show: false,
				min: 0
			}
		});
	});
});
