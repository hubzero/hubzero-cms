/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var charts = [],
		month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	/**
	 * Helper: find elements and iterate
	 */
	function each(selector, callback) {
		var els = document.querySelectorAll(selector);
		for (var i = 0; i < els.length; i++) {
			callback(i, els[i]);
		}
	}

	each('.stats-tickets-chart', function (i, el) {
		var dataEl = document.getElementById(el.getAttribute('data-datasets'));
		if (!dataEl) return;
		var data = dataEl.content ? dataEl.content.textContent : dataEl.innerHTML;
		var datasets = JSON.parse(data);

		// $.plot is from the Flot charting library — it requires jQuery.
		// We keep this call as-is since Flot has no vanilla equivalent;
		// the page must still load jQuery + Flot for charting to work.
		if (typeof jQuery !== 'undefined' && typeof jQuery.plot === 'function') {
			var line = jQuery.plot(jQuery(el), datasets.datasets, {
				series: {
					lines: {
						show: true,
						fill: true
					},
					points: {
						show: false
					},
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
					content: '%y %s in %x',
					shifts: {
						x: -60,
						y: 25
					},
					defaultTheme: false
				},
				legend: {
					show: true,
					noColumns: 2,
					position: 'nw',
					margin: [5, 5]
				},
				xaxis: {
					mode: 'time',
					tickLength: 0,
					tickDecimals: 0,
					tickFormatter: function (val, axis) {
						var d = new Date(val);
						return month_short[d.getUTCMonth()];
					}
				},
				yaxis: {
					min: 0
				}
			});

			charts.push(line);
		}
	});

	each('.stats-pie-chart', function (i, el) {
		var dataEl = document.getElementById(el.getAttribute('data-datasets'));
		if (!dataEl) return;
		var data = dataEl.content ? dataEl.content.textContent : dataEl.innerHTML;
		var datasets = JSON.parse(data);

		if (typeof jQuery !== 'undefined' && typeof jQuery.plot === 'function') {
			var pie = jQuery.plot(jQuery(el), datasets.datasets, {
				legend: {
					show: false
				},
				series: {
					pie: {
						show: true,
						stroke: {
							color: '#efefef'
						}
					}
				},
				grid: {
					hoverable: false
				}
			});

			charts.push(pie);
		}
	});

	each('.stats-user-chart', function (i, el) {
		var dataEl = document.getElementById(el.getAttribute('data-datasets'));
		if (!dataEl) return;
		var data = dataEl.content ? dataEl.content.textContent : dataEl.innerHTML;
		var datasets = JSON.parse(data);

		if (typeof jQuery !== 'undefined' && typeof jQuery.plot === 'function') {
			var user = jQuery.plot(jQuery(el), datasets.datasets, {
				series: {
					lines: {
						show: true,
						fill: true
					},
					points: {
						show: false
					},
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
					content: '%y %s in %x',
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
					mode: 'time',
					tickLength: 0,
					tickDecimals: 0,
					tickFormatter: function (val, axis) {
						var d = new Date(val);
						return month_short[d.getUTCMonth()];
					}
				},
				yaxis: {
					min: 0,
					max: datasets.top
				}
			});

			charts.push(user);
		}
	});
});
