<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$this->css('impact');

if ($this->pub->exists())
{
	$typetitle = \Components\Publications\Helpers\Html::writePubCategory($this->pub->category()->alias, $this->pub->category()->name);
}

$thisMonth = date('M Y');
$lastMonth = date('M Y', strtotime("-1 month"));

$nowMonth = date('M');
$oneMonth = date('M', strtotime("-1 month"));
$twoMonth = date('M', strtotime("-2 month"));
$threeMonth = date('M', strtotime("-3 month"));

$i = 0;
$xticks = "[0, '" . $threeMonth . "'], [1, '" . $twoMonth . "'], [2, '" . $oneMonth . "'], [3, '" . $nowMonth . "']";

// Common options for js charts
$options = "
xaxis: { ticks: xticks },
yaxis: { ticks: [[0, ''], [yTickSize, yTickSize]], color: 'transparent', tickDecimals:0, labelWidth: 0 },
series: {
	lines: {
		show: true,
		fill: true,
		fillColor: fillCol
	},
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
	shifts: {
		x: 0,
		y: -25
	},
	defaultTheme: false
}";

$base = rtrim(Request::base(true), '/');
?>
<div id="plg-header">
<?php if ($this->project->isProvisioned() ) { ?>
<h3 class="prov-header"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> <?php if ($this->pub->exists()) { ?>  &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo Route::url($this->pub->link('editversion')); ?>"><?php echo $this->pub->title; ?></a></span><?php } ?> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS')); ?></h3>
<?php } else { ?>
<h3 class="publications c-header"><a href="<?php echo Route::url($this->project->link('publications')); ?>"><?php echo $this->title; ?></a> <?php if ($this->pub->exists()) { ?>  &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo Route::url($this->pub->link('editversion')); ?>"><?php echo $this->pub->title; ?></a></span><?php } ?> &raquo; <span class="indlist"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS')); ?></span></h3>
<?php } ?>
</div>

<div class="pubstats">
<?php if ($this->pubstats) {
?>
<?php if ($this->pub) { ?>
<p class="viewallstats"><a href="<?php echo Route::url($this->project->link('publications') . '&action=stats'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL_USAGE_STATS'); ?> &raquo;</a></p>
<?php } ?>
<?php if (!$this->pub->exists() && $this->totals && count($this->pubstats) > 1) { ?>
<p class="pubstats-overall"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_YOUR') . ' <span class="prominent">' . count($this->pubstats) . '</span> ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS_S') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_HAVE_BEEN_ACCESSED') . ' <span class="prominent">' . $this->totals->all_total_primary . '</span> ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_TIMES'); ?>.</p>
<?php } ?>
<script src="<?php echo $base; ?>/core/assets/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo $base; ?>/core/assets/js/flot/jquery.flot.time.min.js"></script>
<script src="<?php echo $base; ?>/core/assets/js/flot/jquery.flot.pie.min.js"></script>
<script src="<?php echo $base; ?>/core/assets/js/flot/jquery.flot.resize.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo $base; ?>/core/assets/js/excanvas/excanvas.min.js"></script><![endif]-->

<?php
	foreach ($this->pubstats as $stat)
	{
		$toDate = strtotime($stat->first_published) > strtotime($this->firstlog) ? $stat->first_published : $this->firstlog;

		$yTickSize = max($stat->threemonth_views, $stat->twomonth_views, $stat->lastmonth_views,
			$stat->thismonth_views, $stat->threemonth_primary, $stat->twomonth_primary,
			$stat->lastmonth_primary, $stat->thismonth_primary);

		$i++;
		?>
			<table class="pubstats-wrap">
				<tr><td colspan="6" class="pubstats-h">
					<img src="<?php echo Route::url('index.php?option=com_publications&id=' . $stat->publication_id . '&v=' . $stat->publication_version_id) . '/Image:thumb'; ?>" alt=""/>
					<span class="h-title"><a href="<?php echo Route::url('index.php?option=com_publications&id=' . $stat->publication_id . '&v=' . $stat->version_number); ?>"><?php echo $stat->title; ?></a></span>
					<span class="block mini faded"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLISHED') . ' ' . Date::of($stat->published_up)->toLocal('M d, Y') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN') . ' ' . $stat->cat_name; ?></span>
				</td></tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td><?php
						echo '<span class="pubstats-label">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_THIS_MONTH') . '</span><span class="pubstats-note">' . $thisMonth . '</span>';
					?></td>

					<td><?php echo '<span class="pubstats-label">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_LAST_MONTH') . '</span><span class="pubstats-note">' . $lastMonth . '</span>';  ?></td>

					<td><?php echo '<span class="pubstats-label"><span class="prominent">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_TOTAL') . '</span>*</span><span class="pubstats-note">*' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_SINCE') . ' ' .Date::of($toDate)->toLocal('M d, Y') . ' ' . '</span>';  ?></td>

				</tr>
				<tr>
					<td class="pubstats-sh"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_VIEWS'); ?> <?php if ($i == 1) { ?> <span class="info-pop tooltips" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_VIEWS_TIPS_TITLE_ABOUT'); ?>">&nbsp;</span> <?php } ?></td>
					<td class="pubstats-graph">
						<div id="view-<?php echo $stat->publication_id; ?>" class="ph"></div>
						<script type="text/javascript">
							if (!jq) {
								var jq = $;
							}
							if (jQuery()) {
								var $ = jq;

								// Detect Safari browser (interactivity doesn't work somehow)
								var safari = false;
								if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1)
								{
									safari = true;
								}
								var hover  = safari ? false : true;

								function showTooltip(x,y,contents, append)
								{
									$('<div>' + contents + append +'\ </div>').css({
										position: 'absolute',
										display: 'none',
										top: y,
										left: x,
										'border-style': 'solid',
										'border-color': '#CCC',
										'font-size': '0.8em',
										color: '#CCC',
										padding: '0 2px'
									}).appendTo("body").fadeIn(200);
								}

								function showLabels(graph, points, append)
								{
									var graphx = $(graph).offset().left;
									graphx 	   = graphx + 10;
									var graphy = $(graph).offset().top;
									graphy = graphy - 20;

									for (var k = 0; k < points.length; k++)
									{
										for (var m = 0; m < points[k].data.length; m++)
										{
											if (points[k].data[m][0] != null && points[k].data[m][1] != null)
											{

												if (k == 0)
												{
													showTooltip(graphx + points[k].xaxis.p2c(points[k].data[m][0]) - 15,
														graphy + points[k].yaxis.p2c(points[k].data[m][1]) + 10, points[k].data[m][1], append);
												}
												else
												{
													showTooltip(graphx + points[k].xaxis.p2c(points[k].data[m][0]) - 15,
														graphy + points[k].yaxis.p2c(points[k].data[m][1]) - 45, points[k].data[m][1], append);
												}
											}
										}
									}
								}

								var data = [[0, <?php echo $stat->threemonth_views; ?>],
											[1, <?php echo $stat->twomonth_views; ?>],
											[2, <?php echo $stat->lastmonth_views; ?>],
											[3, <?php echo $stat->thismonth_views; ?>]];

								var ph         = $('#view-<?php echo $stat->publication_id; ?>');
								var xticks     = [<?php echo $xticks; ?>];
								var fillCol    = "#f8e7b3";
								var yTickSize  = <?php echo $yTickSize; ?>;
								var tipContent = '%y';

								if (ph)
								{
									var chart = $.plot( ph, [data], {
										<?php echo $options; ?>
									});

									// Show labels in Safari
									if (safari)
									{
										var points = chart.getData();
										showLabels(ph, points, '');
									}
								}
							}
						</script>
					</td>
					<td></td>
					<td><span class="stats-num"><?php echo $stat->thismonth_views; ?></span></td>
					<td><span class="stats-num"><?php echo $stat->lastmonth_views; ?></span></td>
					<td><span class="stats-num"><?php echo $stat->total_views; ?></span></td>
				</tr>
				<tr>
					<td class="pubstats-sh"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_ACCESSES'); ?> <?php if ($i == 1) { ?> <span class="info-pop tooltips" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_ACCESSES_TIPS_TITLE_ABOUT'); ?>">&nbsp;</span> <?php } ?></td>
					<td class="pubstats-graph"><div id="access-<?php echo $stat->publication_id; ?>" class="ph"></div>
					<script type="text/javascript">
						if (jQuery()) {
							var $ = jq;

							var data = [[0, <?php echo $stat->threemonth_primary; ?>],
										[1, <?php echo $stat->twomonth_primary; ?>],
										[2, <?php echo $stat->lastmonth_primary; ?>],
										[3, <?php echo $stat->thismonth_primary; ?>]];

							var ph         = $('#access-<?php echo $stat->publication_id; ?>');
							var xticks     = [<?php echo $xticks; ?>];
							var fillCol    = "#cdf0c1";
							var yTickSize  = <?php echo $yTickSize; ?>;
							var tipContent = '%y';

							if (ph)
							{
								var chart = $.plot( ph, [data], {
									<?php echo $options; ?>,
									colors: ["#aed3a1"]
								});

								// Show labels in Safari
								if (safari)
								{
									var points = chart.getData();
									showLabels(ph, points, '');
								}
							}
						}
					</script></td>
					<td></td>
					<td><span class="stats-num"><?php echo $stat->thismonth_primary; ?></span></td>
					<td><span class="stats-num"><?php echo $stat->lastmonth_primary; ?></span></td>
					<td><span class="stats-num"><?php echo $stat->total_primary; ?></span></td>
				</tr>
			</table>
<?php }
	} else { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATS_NO_INFO'); ?></p>
<?php } ?>
</div>
