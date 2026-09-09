/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * Project reports: Flot chart initialization
 * Extracted from inline scripts for CSP compliance.
 *
 * Each chart container (.ph[data-chart-data]) receives:
 *   data-chart-data     JSON array of [x, y] points
 *   data-chart-ticks    JSON array of [x, "label"] ticks
 *   data-chart-yticksize  Number for y-axis tick size
 *   data-chart-tip-format  Tooltip format string ("%y" or "%y%")
 *   data-chart-label-append  Append string for Safari labels ("" or "%")
 */
;(function ($) {
	'use strict';

	// Detect Safari browser (interactivity doesn't work with Flot)
	var safari = navigator.userAgent.indexOf('Safari') !== -1
		&& navigator.userAgent.indexOf('Chrome') === -1;
	var hover = !safari;

	function showTooltip(x, y, contents, append) {
		$('<div>' + contents + append + '</div>').css({
			position: 'absolute',
			display: 'none',
			top: y,
			left: x,
			'border-style': 'solid',
			'border-color': '#CCC',
			'font-size': '0.8em',
			color: '#CCC',
			padding: '0 2px'
		}).appendTo('body').fadeIn(200);
	}

	function showLabels(graph, points, append) {
		var graphx = $(graph).offset().left + 10;
		var graphy = $(graph).offset().top - 20;

		for (var k = 0; k < points.length; k++) {
			for (var m = 0; m < points[k].data.length; m++) {
				if (points[k].data[m][0] != null && points[k].data[m][1] != null) {
					var xPos = graphx + points[k].xaxis.p2c(points[k].data[m][0]) - 15;
					var yOffset = k === 0 ? 10 : -45;
					showTooltip(
						xPos,
						graphy + points[k].yaxis.p2c(points[k].data[m][1]) + yOffset,
						points[k].data[m][1],
						append
					);
				}
			}
		}
	}

	function initChart(el) {
		var $el = $(el);
		var data = $el.data('chart-data');
		var xticks = $el.data('chart-ticks');
		var yTickSize = $el.data('chart-yticksize') || 0;
		var tipContent = $el.data('chart-tip-format') || '%y';
		var labelAppend = $el.data('chart-label-append') || '';

		if (!data || !$el.length) {
			return;
		}

		var options = {
			xaxis: { ticks: xticks },
			yaxis: {
				ticks: [[0, ''], [yTickSize, yTickSize]],
				color: 'transparent',
				tickDecimals: 0,
				labelWidth: 0
			},
			series: {
				lines: { show: true, fill: true },
				points: { show: true },
				shadowSize: 0
			},
			grid: {
				color: 'rgba(0, 0, 0, 0.6)',
				borderWidth: 0,
				borderColor: 'transparent',
				hoverable: hover,
				clickable: true,
				minBorderMargin: 10
			},
			tooltip: true,
			tooltipOpts: {
				content: tipContent,
				shifts: { x: 0, y: -25 },
				defaultTheme: false
			}
		};

		var chart = $.plot($el, [data], options);

		// Show labels in Safari since hover tooltips don't work
		if (safari) {
			showLabels(el, chart.getData(), labelAppend);
		}
	}

	$(document).ready(function () {
		$('.ph[data-chart-data]').each(function () {
			initChart(this);
		});
	});

})(jQuery);
