<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Set date time format
$dateFormat = '%d %b %Y';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = true;
}

// Push scripts to document
ximport('Hubzero_Document');
Hubzero_Document::addPluginStylesheet('resources', 'usage');
$document = JFactory::getDocument();
if (!JPluginHelper::isEnabled('system', 'jquery'))
{
	$document->addScript(DS . 'plugins' . DS . 'resources' . DS . 'usage' . DS . 'js' . DS . 'jquery.min.js');
}
$document->addScript(DS . 'plugins' . DS . 'resources' . DS . 'usage' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.min.js');
$document->addScript(DS . 'plugins' . DS . 'resources' . DS . 'usage' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.selection.js');
$document->addScript(DS . 'plugins' . DS . 'resources' . DS . 'usage' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.pie.min.js');
$document->addScript(DS . 'plugins' . DS . 'resources' . DS . 'usage' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.crosshair.min.js');


// Set the base URL
if ($this->resource->alias) {
	$url = 'index.php?option=' . $this->option . '&alias=' . $this->resource->alias . '&active=usage';
} else {
	$url = 'index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=usage';
}

$img1 = $this->chart_path . $this->dthis . '-' . $this->period . '-' . $this->resource->id . '-Users.gif';
$img2 = $this->chart_path . $this->dthis . '-' . $this->period . '-' . $this->resource->id . '-Jobs.gif';

$cls = 'even';

$database =& JFactory::getDBO();

$topvals = new ResourcesStatsToolsTopvals($database);

if (intval($this->params->get('cache', 1)))
{
	$cache =& JFactory::getCache('callback');
	$cache->setCaching(1);
	$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
	$results = $cache->call(array('plgResourcesUsage', 'getOverview'), $this->resource->id);
}
else 
{
	$results = plgResourcesUsage::getOverview($this->resource->id);
}

$users = array();
$interactive = array();
$sessions = array();
$runs = array();

$usersScaled = array();
$runsScaled = array();

$min = (date("Y") - 1) . '/' . date("m") . '/01';
$to = $max = date("Y") . '/' . date("m") . '/01';
$from = (date("Y") - 1) . '/' . date("m") . '/01';
$half = date('Y/m/d', mktime(0, 0, 0, (date("m") - 6), 1, date("Y")));
$qrtr = date('Y/m/d', mktime(0, 0, 0, (date("m") - 3), 1, date("Y")));

if ($results)
{
	$usersTop = 0;
	$runsTop = 0;
	
	$c = count($results);
	foreach ($results as $result)
	{
		$users[]       = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->users . "]";
		$interactive[] = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->sessions . "]";
		$sessions[]    = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->simulations . "]";
		$runs[]        = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->jobs . "]";

		$usersTop = ($result->users > $usersTop) ? $result->users : $usersTop;
		$runsTop = ($result->jobs > $runsTop) ? $result->jobs : $runsTop;
	}

	$min = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $results[0]->datetime));
}
?>
<h3 id="plg-usage-header">
	<a name="usage"></a>
	<?php echo JText::_('PLG_RESOURCES_USAGE'); ?> 
</h3>
<form method="get" action="<?php echo JRoute::_($url); ?>">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo DS . 'plugins' . DS . 'resources' . DS . 'usage' . DS . 'js' . DS . 'excanvas' . DS; ?>excanvas.min.js"></script><![endif]-->
	<div id="u-placeholder-wrapper">
		<div id="u-users-placeholder" style="width:710px;height:200px">
			<h4><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?></h4>
			<strong id="user-overview-total"><?php echo number_format($this->stats->users); ?></strong>
			<div id="user-overview">
			<?php
			if ($results)
			{
				// Find the highest value
				$vals = array();
				foreach ($results as $result)
				{
					$vals[] = $result->users;
				}
				asort($vals);

				$highest = array_pop($vals);
				
				$sparkline  = '<span class="sparkline">' . "\n";
				foreach ($results as $result)
				{
					$height = round(($result->users / $highest)*100);
					$sparkline .= "\t" . '<span class="index">';
					$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . JHTML::_('date', $result->datetime, $dateFormat, $tz) . ': ' . $result->users . '">';
					$sparkline .= $result->users; //trim($this->_fmt_result($result->value, $result->valfmt));
					$sparkline .= '</span> ';
					$sparkline .= '</span>' . "\n";
				}
				$sparkline .= '</span>' . "\n";
				echo $sparkline;
			} 
			?>
				<div class="clear"></div>
			</div>
		</div><!-- / #u-users-placeholder -->
		
		<div id="u-runs-placeholder" style="width:710px;height:200px">
			<h4><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?></h4>
			<strong id="runs-overview-total"><?php echo number_format($this->stats->jobs); ?></strong>
			<div id="runs-overview">
			<?php
				if ($results)
				{
					// Find the highest value
					$vals = array();
					foreach ($results as $result)
					{
						$vals[] = $result->jobs;
					}
					asort($vals);

					$highest = array_pop($vals);

					$sparkline  = '<span class="sparkline">' . "\n";
					foreach ($results as $result)
					{
						$height = round(($result->jobs / $highest)*100);
						$sparkline .= "\t" . '<span class="index">';
						$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . JHTML::_('date', $result->datetime, $dateFormat, $tz) . ': ' . $result->jobs . '">';
						$sparkline .= $result->jobs; //trim($this->_fmt_result($result->value, $result->valfmt));
						$sparkline .= '</span> ';
						$sparkline .= '</span>' . "\n";
					}
					$sparkline .= '</span>' . "\n";
					echo $sparkline;
				} 
			?>
				<div class="clear"></div>
			</div>
		</div><!-- / #u-runs-placeholder -->
		
		<div id="u-overview-wrapper">
			<div id="u-overview" style="width:710;height:100px;"></div>
		</div><!-- / #u-overview-wrapper -->
		
		<p id="u-instructions">
			<?php echo JText::_('Click data point to view breakdowns below'); ?>
		</p>
		
		<p id="set-selection">
			<a class="set-selection selected" rel="<?php echo $from; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=12&dthis=' . $this->dthis); ?>"><?php echo JText::_('Year'); ?></a>
			<a class="set-selection" rel="<?php echo $half; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=13&dthis=' . $this->dthis); ?>"><?php echo JText::_('6 months'); ?></a>
			<a class="set-selection" rel="<?php echo $qrtr; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=3&dthis=' . $this->dthis); ?>"><?php echo JText::_('Quarter'); ?></a>
		</p>
	</div>
		<script type="text/javascript">
	if (!jq) {
		var jq = $;
	}
	if (jQuery()) {
		var $ = jq;
		
		dataurl = '/index.php?option=com_resources&id=<?php echo $this->resource->id; ?>&active=usage&action=top&datetime=';
		
		$(function () {
			var datasets = [
				{
					lines: { fillColor: '<?php echo $this->params->get("chart_color_fill", "rgba(0, 0, 0, 0.1)"); ?>' },
					color: '<?php echo $this->params->get("chart_color_line", "#999"); ?>', //#93ACCA
					label: "<?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?>",
					data: [<?php echo implode(',', $users); ?>]
				},
				{
					lines: {fillColor: '<?php echo $this->params->get("chart_color_fill2", "rgba(207, 207, 171, 0.3)"); ?>' },
					color: '<?php echo $this->params->get("chart_color_line2", "#CFCFAB"); ?>', //#CFCFAB
					label: "<?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?>",
					data: [<?php echo implode(',', $runs); ?>]
				}
			];

			var options = {
				series: {
					lines: { 
						show: true,
						fill: true
					},
					points: { show: true },
					shadowSize: 0
				},
				crosshair: { mode: "x" },
				grid: {
					borderWidth: 1,
					borderColor: 'rgba(0, 0, 0, 0.6)',
					hoverable: true, 
					clickable: true
				},
				legend: { show: true },
				xaxis: { position: 'top', mode: "time", min: new Date('<?php echo $from; ?>'), max: new Date('<?php echo $to; ?>'), tickDecimals: 0 },
				yaxis: { min: 0, labelWidth: 25 }
			};

			var choiceContainer = $("#choices");

			// Function for populating a (pie chart) table
			function populateTable(id, data) {
				var tbl = $('#' + id + ' tbody');

				tbl.empty();

				var footer = data.shift();
				var total = footer['data'];
				for (var i=0; i < data.length; i++)
				{
					tbl.append(
						'<tr>' +
							'<th><span style="background-color: ' + data[i]['color'] + '">' + i + '</span></th>' + 
							'<td class="textual-data">' + data[i]['label'] + '</td>' + 
							'<td>' + data[i]['data'] + '</td>' + 
							'<td>' + Math.round(((data[i]['data']/total)*100),2) + '</td>' + 
						'</tr>'
					);
				}
				tbl.append('<tr class="summary">' +
					'<td> </td>' + 
					'<td class="textual-data">Total Users</td>' + 
					'<td>' + total + '</td>' + 
					'<td>100</td>' + 
				'</tr>');
				data.unshift(footer);
			}
			
			// Function for showing tooltips
			function showTooltip(x, y, contents) {
				$('<div id="u-tooltip">' + contents + '</div>').css({
					position: 'absolute',
					display: 'none',
					top: y + 5,
					left: x + 5
	 			}).appendTo("body").fadeIn(200);
			}
			
			var previousPoint = null;
			var latestPosition = null;
			
			function plotAccordingToChoices() {
					// User plot
					// ---------
					var placeholderU = $("#u-users-placeholder");
					// Bind the selection area so the chart updates
					placeholderU.bind("plotselected", function (event, ranges) {
						if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
							ranges.xaxis.to = ranges.xaxis.from + 0.00001;
						}
						if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
							ranges.yaxis.to = ranges.yaxis.from + 0.00001;
						}
						plotU = $.plot($("#u-users-placeholder"), [datasets[0]],
							$.extend(true, {}, options, {
								xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
							}));

						// don't fire event on the overview to prevent eternal loop
						overview.setSelection(ranges, true);
					});
					// Bind click events to update pie graphs when a plot point is selected
					placeholderU.bind("plotclick", function (event, pos, item) {
						if (item) {
							var mm = item.series.data[item.dataIndex][0].getMonth()+1; // January is 0!
							var yyyy = item.series.data[item.dataIndex][0].getFullYear();
							// Prepend 0s
							if (mm < 10) {
								mm = '0' + mm
							}
							// Update organizations pie chart
							/*if (orgData[yyyy + '/' + mm + '/01'].length > 0) {
								populateTable('pie-org-data', orgData[yyyy + '/' + mm + '/01']);
								$.plot($("#pie-org"), orgData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
							}
							// Update countries pie chart
							if (countryData[yyyy + '/' + mm + '/01'].length > 0) {
								populateTable('pie-country-data', countryData[yyyy + '/' + mm + '/01']);
								$.plot($("#pie-country"), countryData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
							}
							// Update domains pie chart
							if (domainData[yyyy + '/' + mm + '/01'].length > 0) {
								populateTable('pie-domains-data', domainData[yyyy + '/' + mm + '/01']);
								$.plot($("#pie-domains"), domainData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
							}*/
							$.getJSON(dataurl + yyyy + '-' + mm, function(series){
								if (!orgData[yyyy + '/' + mm + '/01']) {
									orgData[yyyy + '/' + mm + '/01'] = series.orgs[yyyy + '/' + mm + '/01'];
								}
								if (!countryData[yyyy + '/' + mm + '/01']) {
									countryData[yyyy + '/' + mm + '/01'] = series.countries[yyyy + '/' + mm + '/01'];
								}
								if (!domainData[yyyy + '/' + mm + '/01']) {
									domainData[yyyy + '/' + mm + '/01'] = series.domains[yyyy + '/' + mm + '/01'];
								}
								// Update organizations pie chart
								if (orgData[yyyy + '/' + mm + '/01'].length > 0) {
									populateTable('pie-org-data', orgData[yyyy + '/' + mm + '/01']);
									$.plot($("#pie-org"), orgData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
								}
								// Update countries pie chart
								if (countryData[yyyy + '/' + mm + '/01'].length > 0) {
									populateTable('pie-country-data', countryData[yyyy + '/' + mm + '/01']);
									$.plot($("#pie-country"), countryData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
								}
								// Update domains pie chart
								if (domainData[yyyy + '/' + mm + '/01'].length > 0) {
									populateTable('pie-domains-data', domainData[yyyy + '/' + mm + '/01']);
									$.plot($("#pie-domains"), domainData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
								}
							});

							// Unhighlight any previously clicked points
							plotU.unhighlight();
							plotR.unhighlight();
							// Highlight the current point
							plotU.highlight(item.series, item.datapoint);
						}
					});
					placeholderU.bind("plothover", function (event, pos, item) {
						if (item) {
							if (previousPoint != item.dataIndex) {
								previousPoint = item.dataIndex;

								$("#u-tooltip").remove();
								var x = item.datapoint[0].toFixed(2),
									y = item.datapoint[1].toFixed(2);

								showTooltip(item.pageX, item.pageY, datasets[0].data[item.dataIndex][1]);
							}
						} else {
							$("#u-tooltip").remove();
							previousPoint = null;
						}
						//sync crosshairs of the other two plots
						plotR.setCrosshair(pos);
					});
					// Generate the plot
					var plotU = $.plot(placeholderU, [datasets[0]], options);

					// Runs (jobs) plot
					// ----------------
					var placeholderR = $("#u-runs-placeholder");
					// Bind the selection area so the chart updates
					placeholderR.bind("plotselected", function (event, ranges) {
						if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
							ranges.xaxis.to = ranges.xaxis.from + 0.00001;
						}
						if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
							ranges.yaxis.to = ranges.yaxis.from + 0.00001;
						}
						plotR = $.plot($("#u-runs-placeholder"), [datasets[1]],
							$.extend(true, {}, options, {
								xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
							}));

						// don't fire event on the overview to prevent eternal loop
						overview.setSelection(ranges, true);
					});
					// Bind click events to update pie graphs when a plot point is selected
					placeholderR.bind("plotclick", function (event, pos, item) {
						if (item) {
							var mm = item.series.data[item.dataIndex][0].getMonth()+1; // January is 0!
							var yyyy = item.series.data[item.dataIndex][0].getFullYear();
							// Prepend 0s
							if (mm < 10) {
								mm = '0' + mm
							}

							$.getJSON(dataurl + yyyy + '-' + mm, function(series){
								if (!orgData[yyyy + '/' + mm + '/01']) {
									orgData[yyyy + '/' + mm + '/01'] = series.orgs[yyyy + '/' + mm + '/01'];
								}
								if (!countryData[yyyy + '/' + mm + '/01']) {
									countryData[yyyy + '/' + mm + '/01'] = series.countries[yyyy + '/' + mm + '/01'];
								}
								if (!domainData[yyyy + '/' + mm + '/01']) {
									domainData[yyyy + '/' + mm + '/01'] = series.domains[yyyy + '/' + mm + '/01'];
								}
								// Update organizations pie chart
								if (orgData[yyyy + '/' + mm + '/01'].length > 0) {
									populateTable('pie-org-data', orgData[yyyy + '/' + mm + '/01']);
									$.plot($("#pie-org"), orgData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
								}
								// Update countries pie chart
								if (countryData[yyyy + '/' + mm + '/01'].length > 0) {
									populateTable('pie-country-data', countryData[yyyy + '/' + mm + '/01']);
									$.plot($("#pie-country"), countryData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
								}
								// Update domains pie chart
								if (domainData[yyyy + '/' + mm + '/01'].length > 0) {
									populateTable('pie-domains-data', domainData[yyyy + '/' + mm + '/01']);
									$.plot($("#pie-domains"), domainData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
								}
							});

							// Update organizations pie chart
							/*if (orgData[yyyy + '/' + mm + '/01'].length > 0) {
								populateTable('pie-org-data', orgData[yyyy + '/' + mm + '/01']);
								$.plot($("#pie-org"), orgData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
							}
							// Update countries pie chart
							if (countryData[yyyy + '/' + mm + '/01'].length > 0) {
								populateTable('pie-country-data', countryData[yyyy + '/' + mm + '/01']);
								$.plot($("#pie-country"), countryData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
							}
							// Update domains pie chart
							if (domainData[yyyy + '/' + mm + '/01'].length > 0) {
								populateTable('pie-domains-data', domainData[yyyy + '/' + mm + '/01']);
								$.plot($("#pie-domains"), domainData[yyyy + '/' + mm + '/01'].slice(1), pieOptions);
							}*/
							// Unhighlight any previously clicked points
							plotU.unhighlight();
							plotR.unhighlight();
							// Highlight the current point
							plotR.highlight(item.series, item.datapoint);
						}
					});
					placeholderR.bind("plothover", function (event, pos, item) {
						if (item) {
							if (previousPoint != item.dataIndex) {
								previousPoint = item.dataIndex;

								$("#u-tooltip").remove();
								var x = item.datapoint[0].toFixed(2),
									y = item.datapoint[1].toFixed(2);

								showTooltip(item.pageX, item.pageY, datasets[1].data[item.dataIndex][1]);
							}
						} else {
							$("#u-tooltip").remove();
							previousPoint = null;
						}
						//sync crosshairs of the other two plots
						plotU.setCrosshair(pos);
					});
					// Generate the plot
					var plotR = $.plot(placeholderR, [datasets[1]], options);

					//var legends = $("#u-placeholder-wrapper .legendLabel");

					// Overview plot
					// -------------
					var overview = $.plot($("#u-overview"), datasets, {
						legend: { show: false },
						series: {
							points: { show: false },
							lines: {
								show: true, 
								lineWidth: 1,
								fill: true, 
								fillColor: '<?php echo $this->params->get("chart_color_fill", "rgba(0, 0, 0, 0.085)"); ?>'
							},
							shadowSize: 0
						},
						grid: {
							borderWidth: 1,
							borderColor: 'rgba(0, 0, 0, 0.6)'
						},
						xaxis: { mode: "time", min: new Date('<?php echo $min; ?>'), max: new Date('<?php echo $to; ?>'), tickDecimals: 0 },
						yaxis: { color: '#fff', min: 0, autoscaleMargin: 0.1, labelWidth: 25 },
						selection: { 
							mode: "x", 
							color: '<?php echo $this->params->get("chart_color_selection", "rgba(0, 0, 0, 0.3)"); ?>', 
							navigate: true 
						}
					});
					overview.setSelection({ 
							xaxis: {
								from: new Date('<?php echo $from; ?>'),
								to: new Date('<?php echo $to; ?>')
							}
						}, 
						true
					);
					$("#u-overview").unbind("plotselected");
					$("#u-overview").unbind("plotnavigating");

					$("#u-overview").bind("plotselected", function (event, ranges) {
						plotU.setSelection(ranges);
						plotR.setSelection(ranges);
					});
					$("#u-overview").bind("plotnavigating", function (event, ranges) {
						$("#u-tooltip").remove();
						previousPoint = null;
						overview.getPlaceholder().css('cursor', 'col-resize');
						plotU.setSelection(ranges);
						plotR.setSelection(ranges);
					});
					$("#u-overview").bind("plotnavigated", function (event, ranges) {
						overview.getPlaceholder().css('cursor', 'default');
					});

					// Allow for window resixing
					//overview.resize();
					$(window).resize(function() {
						if (this.resizeTO) clearTimeout(this.resizeTO);
						this.resizeTO = setTimeout(function() {
							$(this).trigger('resizeEnd');
						}, 100);
					});
					$(window).bind('resizeEnd', function() {
						if (plotU) {
							plotU.resize();
							plotU.setupGrid();
							$('#u-users-placeholder .tickLabels').each(function(i, item){
								if (i == 0) {
									$(item).remove();
								}
							});
							plotU.draw();
						}
						if (plotR) {
							plotR.resize();
							plotR.setupGrid();
							$('#u-runs-placeholder .tickLabels').each(function(i, item){
								if (i == 0) {
									$(item).remove();
								}
							});
							plotR.draw();
						}
						if (overview) {
							overview.resize();
							overview.setupGrid();
							$('#u-overview .tickLabels').each(function(i, item){
								if (i == 0) {
									$(item).remove();
								}
							});
							overview.draw();
						}
					});
					
					$('.set-selection').click(function (e) {
						e.preventDefault();

						$('.set-selection').each(function(i, el) {
							$(el).removeClass('selected');
						});
						$(this).addClass('selected');

						var sizeTokens = $(this).attr('rel').split(' ');
						var from = sizeTokens[0];
						var to = sizeTokens[1];

						plotU = $.plot($("#u-users-placeholder"), [datasets[0]],
							$.extend(true, {}, options, {
								xaxis: { min: new Date(from), max: new Date(to) }
							}));
						plotR = $.plot($("#u-runs-placeholder"), [datasets[1]],
							$.extend(true, {}, options, {
								xaxis: { min: new Date(from), max: new Date(to) }
							}));

						// don't fire event on the overview to prevent eternal loop
						overview.setSelection({
								xaxis: {
									from: new Date(from), 
									to: new Date(to)
								}
							}, 
							true
						);
					});
				//}
			}

			plotAccordingToChoices();
		});
	}
		</script>

		<div style="clear:left;"></div>

		<div class="two columns first">
			<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?>" id="pie-org-data" class="pie-chart">
				<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?></caption>
				<thead>
					<tr>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
						<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TYPE'); ?></th>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 

$colors = array(
	$this->params->get('pie_chart_color1', '#7c7c7c'),
	$this->params->get('pie_chart_color2', '#515151'),
	$this->params->get('pie_chart_color3', '#d9d9d9'),
	$this->params->get('pie_chart_color4', '#3d3d3d'),
	$this->params->get('pie_chart_color5', '#797979'),
	$this->params->get('pie_chart_color6', '#595959'),
	$this->params->get('pie_chart_color7', '#e5e5e5'),
	$this->params->get('pie_chart_color8', '#828282'),
	$this->params->get('pie_chart_color9', '#404040'),
	$this->params->get('pie_chart_color10', '#6a6a6a'),
	$this->params->get('pie_chart_color1', '#bcbcbc'),
	$this->params->get('pie_chart_color2', '#515151'),
	$this->params->get('pie_chart_color3', '#d9d9d9'),
	$this->params->get('pie_chart_color4', '#3d3d3d'),
	$this->params->get('pie_chart_color5', '#797979'),
	$this->params->get('pie_chart_color6', '#595959'),
	$this->params->get('pie_chart_color7', '#e5e5e5'),
	$this->params->get('pie_chart_color8', '#828282'),
	$this->params->get('pie_chart_color9', '#404040'),
	$this->params->get('pie_chart_color10', '#3a3a3a'),
);

$datetime = date("Y") . '-' . date("m");
$tid = plgResourcesUsage::getTid($this->resource->id, $datetime);

if (intval($this->params->get('cache', 1)))
{
	$cache =& JFactory::getCache('callback');
	$cache->setCaching(1);
	$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
	$results = $cache->call(array('plgResourcesUsage', 'getTopValue'), $this->resource->id, 3, $tid, $datetime);
}
else 
{
	$results = plgResourcesUsage::getTopValue($this->resource->id, 3, $tid, $datetime);
}
$data = array();
$r = array();
$results = null;
$total = 0;
$cls = 'even';
$tot = '';
$pieOrg = array();
$toporgs = null;
if ($results)
{
	$i = 0;
	$data = array();
	$r = array();
	foreach ($results as $row)
	{
		$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
		if (!isset($data[$ky]))
		{
			$i = 0;
			$data[$ky] = array();
			$r[$ky] = array();
		}
		$data[$ky][] = $row;
		if (!isset($colors[$i]))
		{
			$i = 0;
		}
		$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}';
		$i++;
	}
	$toporgs = end($data);
}

//$toporgs = $topvals->getTopCountryRes($this->stats->id, 3);
$nd = '';
if ($toporgs) {
	$i = 0;
	foreach ($toporgs as $row)
	{
		$total += $row->value;
	}
	foreach ($toporgs as $row)
	{
		if ($row->name == '?') {
			$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

		if ($row->rank == '0') {
			$nd = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
			$total = $row->value;
			if ($total) {
				$tot = '<tr class="summary">
					<td> </td>
					<td class="textual-data">'.$row->name.'</td>
					<td>'.number_format($row->value).'</td>
					<td>'.round((($row->value/$total)*100),2).'</td>
				</tr>';
			}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
					<tr rel="<?php echo $row->name; ?>">
						<th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th>
						<td class="textual-data"><?php echo $row->name; ?></td>
						<td><?php echo number_format($row->value); ?></td>
						<td><?php echo round((($row->value/$total)*100),2); ?></td>
					</tr>
<?php
			$i++;
		}
	}
}
else 
{
?>
					<tr>
						<td colspan="4" class="textual-data"><?php echo JText::sprintf('No data found for the month of %s', $to); ?></td>
					</tr>
<?php
}
//echo $tot;
?>
				</tbody>
			</table>
		</div>
		<div class="two columns second">
			<div style="text-align: center; margin-top: 5em; position: relative;">
				<div id="pie-org" style="width:320px; height:320px"></div>
			</div>
			<script>
			if (jQuery()) {
				var $ = jq;
				var pieOptions = {
					legend: { 
						show: false 
					},
					series: {
						pie: { 
							innerRadius: 0.5,
							show: true,
							label: { show: false }
						}
					},
					grid: {
						hoverable: false
					}
				};
				
				var orgData = {
				<?php
				$z = array();
				foreach ($r as $k => $d)
				{
					$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
				}
				echo implode(',', $z);
				?>
				};

				if (typeof orgData['<?php echo $nd; ?>'] != 'undefined' && orgData['<?php echo $nd; ?>'].length > 0) {
					$.plot($("#pie-org"), orgData['<?php echo $nd; ?>'], pieOptions);
				}
			}
			</script>
		</div>
		<div class="clear"></div>
		
		<div class="two columns first">
			<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?>" id="pie-country-data" class="pie-chart">
				<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?></caption>
				<thead>
					<tr>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
						<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_COUNTRY'); ?></th>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 
//$topcountries = $topvals->getTopCountryRes($this->stats->id, 1);

if (intval($this->params->get('cache', 1)))
{
	$cache =& JFactory::getCache('callback');
	$cache->setCaching(1);
	$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
	$results = $cache->call(array('plgResourcesUsage', 'getTopValue'), $this->resource->id, 1, $tid, $datetime);
}
else 
{
	$results = plgResourcesUsage::getTopValue($this->resource->id, 1, $tid, $datetime);
}
$results = null;
$topcountries = null;
$i = 0;
if ($results)
{
	$data = array();
	$r = array();
	foreach ($results as $row)
	{
		$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
		if (!isset($data[$ky]))
		{
			$i = 0;
			$data[$ky] = array();
			$r[$ky] = array();
		}
		$data[$ky][] = $row;
		if (!isset($colors[$i]))
		{
			$i = 0;
		}
		$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}'."\n";
		$i++;
	}
	$topcountries = end($data);
}

$total = '';
$cls = 'even';
$tot = '';
$pie = array();
$i = 0;
if ($topcountries && count($topcountries) > 0) 
{
	foreach ($topcountries as $row)
	{
		if ($row->name == '?') {
			$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

		if ($row->rank == '0') {
			$nd = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
			$total = $row->value;
			if ($total) {
				$tot = '<tr class="summary">
					<td> </td>
					<td class="textual-data">'.$row->name.'</td>
					<td>'.number_format($row->value).'</td>
					<td>'.round((($row->value/$total)*100),2).'</td>
				</tr>';
			}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
					<tr rel="<?php echo $row->name; ?>">
						<th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th>
						<td class="textual-data"><?php echo $row->name; ?></td>
						<td><?php echo number_format($row->value); ?></td>
						<td><?php echo round((($row->value/$total)*100),2); ?></td>
					</tr>
<?php
			$i++;
		}
	}
}
else 
{
?>
					<tr>
						<td colspan="4" class="textual-data"><?php echo JText::sprintf('No data found for the month of %s', $to); ?></td>
					</tr>
<?php
}
echo $tot;
?>
				</tbody>
			</table>
		</div>
		<div class="two columns second">
			<div style="text-align: center; margin-top: 5em; position: relative;">
				<div id="pie-country" style="width:320px; height:320px"></div>
			</div>
			<script>
			if (jQuery()) {
				var $ = jq;

				var countryData = {
					<?php
					$z = array();
					foreach ($r as $k => $d)
					{
						$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
					}
					echo implode(',', $z);
					?>
				};

				if (typeof countryData['<?php echo $nd; ?>'] != 'undefined' && countryData['<?php echo $nd; ?>'].length > 0) {
					$.plot($("#pie-country"), countryData['<?php echo $nd; ?>'], pieOptions);
				}
			}
			</script>
		</div>
		<div class="clear"></div>
		
		<div class="two columns first">
			<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?>" id="pie-domains-data" class="pie-chart">
				<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?></caption>
				<thead>
					<tr>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
						<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_DOMAINS'); ?></th>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
						<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 
//$topdoms = $topvals->getTopCountryRes($this->stats->id, 2);

if (intval($this->params->get('cache', 1)))
{
	$cache =& JFactory::getCache('callback');
	$cache->setCaching(1);
	$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
	$results = $cache->call(array('plgResourcesUsage', 'getTopValue'), $this->resource->id, 2, $tid, $datetime);
}
else 
{
	$results = plgResourcesUsage::getTopValue($this->resource->id, 2, $tid, $datetime);
}
$results = null;
$topdoms = null;
$i = 0;
if ($results)
{
	$data = array();
	$r = array();
	foreach ($results as $row)
	{
		$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
		if (!isset($data[$ky]))
		{
			$i = 0;
			$data[$ky] = array();
			$r[$ky] = array();
		}
		$data[$ky][] = $row;
		if (!isset($colors[$i]))
		{
			$i = 0;
		}
		$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}';
		$i++;
	}
	$topdoms = end($data);
}

$total = '';
$cls = 'even';
$tot = '';
$pie = array();
$i = 0;
if ($topdoms) {
	foreach ($topdoms as $row)
	{
		if ($row->name == '?') {
			$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

		if ($row->rank == '0') {
			$nd = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
			$total = $row->value;
			if ($total) {
				$tot = '<tr class="summary">
					<td> </td>
					<td class="textual-data">'.$row->name.'</td>
					<td>'.number_format($row->value).'</td>
					<td>'.round((($row->value/$total)*100),2).'</td>
				</tr>';
			}
		} else {
			//$pie[] = '{label: \''.$row->name.'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}';
			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
				<tr rel="<?php echo $row->name; ?>">
					<th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th>
					<td class="textual-data"><?php echo $row->name; ?></td>
					<td><?php echo number_format($row->value); ?></td>
					<td><?php echo round((($row->value/$total)*100),2); ?></td>
				</tr>
<?php
			$i++;
		}
	}
}
else 
{
?>
					<tr>
						<td colspan="4" class="textual-data"><?php echo JText::sprintf('No data found for the month of %s', $to); ?></td>
					</tr>
<?php
}
echo $tot;
?>
				</tbody>
			</table>
		</div>
		<div class="two columns second">
			<div style="text-align: center; margin-top: 5em; position: relative;">
				<div id="pie-domains" style="width:320px; height:320px"></div>
			</div>
			<script>
			if (jQuery()) {
				var $ = jq;
				
				var domainData = {
				<?php
				$z = array();
				foreach ($r as $k => $d)
				{
					$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
				}
				echo implode(',', $z);
				?>
				};

				if (typeof domainData['<?php echo $nd; ?>'] != 'undefined' && domainData['<?php echo $nd; ?>'].length > 0) {
					$.plot($("#pie-domains"), domainData['<?php echo $nd; ?>'], pieOptions);
				}
			}
			</script>
		</div>
		<div class="clear"></div>
</form>