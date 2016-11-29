<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=' . $this->option . '&' . ($this->publication->alias ? 'alias=' . $this->publication->alias : 'id=' . $this->publication->id) . '&active=usage';

$db = App::get('db');
$current = new stdClass;
$current->page_views = 0;
$current->primary_accesses = 0;
$current->year  = substr(date("Y"), 2);
$current->month = date("m");
$viewshighest = 0;
$downhighest = 0;

$views = array();
$downloads = array();

$db->setQuery(
	"SELECT *
	FROM `#__publication_logs`
	WHERE `publication_id`=" . $db->quote($this->publication->id) . " AND `publication_version_id`=" . $db->quote($this->publication->version->id) . "
	ORDER BY `year` ASC, `month` ASC"
);
$results = $db->loadObjectList();
if ($results)
{
	foreach ($results as $result)
	{
		$views[]     = "[new Date('20" . $result->year . '-' . \Hubzero\Utility\String::pad($result->month, 2) . "-01')," . $result->page_views . "]";
		$viewshighest = $result->page_views > $viewshighest ? $result->page_views : $viewshighest;
		$downloads[] = "[new Date('20" . $result->year . '-' . \Hubzero\Utility\String::pad($result->month, 2) . "-01')," . $result->primary_accesses . "]";
		$downhighest = $result->primary_accesses > $downhighest ? $result->primary_accesses : $downhighest;
	}

	$current = end($results);
}
$current->datetime = $current->year . '-' . \Hubzero\Utility\String::pad($current->month, 2) . '-01 00:00:00';

$this->css();
$this->js('flot/jquery.colorhelpers.min.js', 'system')
     ->js('flot/jquery.flot.min.js', 'system')
     ->js('flot/jquery.flot.time.min.js', 'system')
     ->js('flot/jquery.flot.resize.min.js', 'system')
     ->js('flot/jquery.flot.canvas.min.js', 'system')
     ->js('flot/jquery.flot.tooltip.min.js', 'system')
     ->js('base64.js')
     ->js('canvas2image.js')
     ->js('jquery.flot.saveAsImage.js');
?>
<h3 id="plg-usage-header">
	<?php echo Lang::txt('PLG_PUBLICATIONS_USAGE'); ?>
</h3>

<form method="get" action="<?php echo Route::url($url); ?>">
<?php if (count($results)) { ?>
	<div class="usage-wrap">
		<div class="grid charts">
			<div class="col span3 usage-stat">
				<h4><?php echo Lang::txt('PLG_PUBLICATIONS_USAGE_VIEWS'); ?></h4>
				<p class="total">
					<strong class="usage-value" id="publication-views"><?php echo number_format($current->page_views); ?></strong>
					<span id="publication-views-date"><time datetime="<?php echo $current->datetime; ?>"><?php echo Date::of($current->datetime)->toLocal('M Y'); ?></time></span></span>
				</p>
			</div>
			<div class="col span9 omega usage-stat">
				<div class="chart-wrap">
					<div id="chart-views" class="chart line"><?php
						$sparkline  = '<span class="sparkline">' . "\n";
						foreach ($results as $result)
						{
							$height = ($viewshighest) ? round(($result->page_views / $viewshighest)*100) : 0;
							$sparkline .= "\t" . '<span class="index">';
							$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="20' . $result->year . '-' . \Hubzero\Utility\String::pad($result->month, 2) . ': ' . $result->page_views . '">';
							$sparkline .= number_format($result->page_views);
							$sparkline .= '</span> ';
							$sparkline .= '</span>' . "\n";
						}
						$sparkline .= '</span>' . "\n";
						echo $sparkline;
					?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="usage-wrap">
		<div class="grid charts">
			<div class="col span3 usage-stat">
				<h4><?php echo Lang::txt('PLG_PUBLICATIONS_USAGE_DOWNLOADS'); ?></h4>
				<p class="total">
					<strong class="usage-value" id="publication-downloads"><?php echo number_format($current->primary_accesses); ?></strong>
					<span id="publication-downloads-date"><time datetime="<?php echo $current->datetime; ?>"><?php echo Date::of($current->datetime)->toLocal('M Y'); ?></time></span></span>
				</p>
			</div>
			<div class="col span9 omega usage-stat">
				<div class="chart-wrap">
					<div id="chart-downloads" class="chart line"><?php
						$sparkline  = '<span class="sparkline">' . "\n";
						foreach ($results as $result)
						{
							$height = ($downhighest) ? round(($result->primary_accesses / $downhighest)*100) : 0;
							$sparkline .= "\t" . '<span class="index">';
							$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="20' . $result->year . '-' . \Hubzero\Utility\String::pad($result->month, 2) . ': ' . $result->primary_accesses . '">';
							$sparkline .= number_format($result->primary_accesses);
							$sparkline .= '</span> ';
							$sparkline .= '</span>' . "\n";
						}
						$sparkline .= '</span>' . "\n";
						echo $sparkline;
					?></div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				chart,
				month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

			$(document).ready(function() {
				var dataset_views = [
					{
						color: "#666666",
						label: "<?php echo Lang::txt('PLG_PUBLICATIONS_USAGE_VIEWS'); ?>",
						data: [<?php echo implode(',', $views); ?>]
					}
				];
				var dataset_downloads = [
					{
						color: "#666666",
						label: "<?php echo Lang::txt('PLG_PUBLICATIONS_USAGE_DOWNLOADS'); ?>",
						data: [<?php echo implode(',', $downloads); ?>]
					}
				];

				function updateCharts(event, pos, item) {
					if (!item) {
						return;
					}

					var mm   = item.series.data[item.dataIndex][0].getMonth()+1; // January is 0!
					var yyyy = item.series.data[item.dataIndex][0].getFullYear();
					// Prepend 0s
					if (mm < 10) {
						mm = '0' + mm
					}

					if (chart_views) {
						chart_views.unhighlight();
						chart_views.highlight(0, item.dataIndex);
					}
					if (chart_downloads) {
						chart_downloads.unhighlight();
						chart_downloads.highlight(0, item.dataIndex);
					}

					$('#publication-views').text(dataset_views[0].data[item.dataIndex][1]);
					$('#publication-views-date').text(month_short[item.series.data[item.dataIndex][0].getMonth()] + ' ' + yyyy);

					$('#publication-downloads').text(dataset_downloads[0].data[item.dataIndex][1]);
					$('#publication-downloads-date').text(month_short[item.series.data[item.dataIndex][0].getMonth()] + ' ' + yyyy);
				};

				var views = $('#chart-views');
				views.bind("plotclick", function(event, pos, item) {
					return updateCharts(event, pos, item);
				});

				var chart_views = $.plot(views, dataset_views, {
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
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							return (d.getUTCMonth() + 1) + "/" + d.getUTCFullYear().toString().substr(2,2);
						}
					},
					yaxis: {
						min: 0,
						tickFormatter: function (val, axis) {
							if (val > 1000) {
								val = (val / 1000) + ' K';
							}
							return val;
						}
					}
				});
				chart_views.highlight(0, <?php echo (count($views) - 1); ?>);


				var downloads = $('#chart-downloads');
				downloads.bind("plotclick", function(event, pos, item) {
					return updateCharts(event, pos, item);
				});

				var chart_downloads = $.plot(downloads, dataset_downloads, {
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
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							return (d.getUTCMonth() + 1) + "/" + d.getUTCFullYear().toString().substr(2,2);
						}
					},
					yaxis: {
						min: 0,
						tickFormatter: function (val, axis) {
							if (val > 1000) {
								val = (val / 1000) + ' K';
							}
							return val;
						}
					}
				});
				chart_downloads.highlight(0, <?php echo (count($downloads) - 1); ?>);
			});
		}
	</script>
<?php } else { ?>
	<div id="no-usage">
		<p class="warning"><?php echo Lang::txt('PLG_PUBLICATIONS_USAGE_NO_DATA_AVAILABLE'); ?></p>
	</div>
<?php } ?>
</form>
