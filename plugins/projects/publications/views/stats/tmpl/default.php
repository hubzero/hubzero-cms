<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->pub)
{
	$typetitle = PublicationHelper::writePubCategory($this->pub->cat_alias, $this->pub->cat_name);
}

$thisMonth = date('M Y');
$lastMonth = date('M Y', strtotime("-1 month"));

$nowMonth = date('M');
$oneMonth = date('M', strtotime("-1 month"));
$twoMonth = date('M', strtotime("-2 month"));
$threeMonth = date('M', strtotime("-3 month"));

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}

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

?>	
<div id="plg-header">
<?php if($this->project->provisioned == 1 ) { ?>
<h3 class="prov-header"><a href="<?php echo $this->route; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> <?php if ($this->pub) { ?>  &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo $this->url . '?version=' . $this->version; ?>"><?php echo $this->pub->title; ?></a></span><?php } ?> &raquo; <?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_STATS')); ?></h3>
<?php } else { ?>
<h3 class="publications c-header"><a href="<?php echo $this->route; ?>"><?php echo $this->title; ?></a> <?php if ($this->pub) { ?>  &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo $this->url . '?version=' . $this->version; ?>"><?php echo $this->pub->title; ?></a></span><?php } ?> &raquo; <span class="indlist"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_STATS')); ?></span></h3>
<?php } ?>
</div>

<div class="pubstats">
<?php if ($this->pubstats) { 
?>
<?php if (!$this->pub && $this->totals && count($this->pubstats) > 1) { ?>	
<p class="pubstats-overall"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_YOUR') . ' <span class="prominent">' . count($this->pubstats) . '</span> ' . JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS_S') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_HAVE_BEEN_ACCESSED') . ' <span class="prominent">' . $this->totals->all_total_primary . '</span> ' . JText::_('PLG_PROJECTS_PUBLICATIONS_TIMES'); ?>.</p>
<?php } ?>
<script src="/media/system/js/flot/jquery.flot.min.js"></script>
<script src="/media/system/js/flot/jquery.flot.tooltip.min.js"></script>
<script src="/media/system/js/flot/jquery.flot.pie.min.js"></script>
<script src="/media/system/js/flot/jquery.flot.resize.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/media/system/js/excanvas/excanvas.min.js"></script><![endif]-->

<?php if ($this->pub) { ?>	
<p class="viewallstats"><a href="<?php echo $this->route . '?action=stats'; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL_USAGE_STATS'); ?> &raquo;</a></p>
<?php } ?>
	
<?php	
	foreach ($this->pubstats as $stat)
	{
		$pubthumb = $this->helper->getThumb($stat->publication_id, 
			$stat->publication_version_id, 
			$this->pubconfig, 
			false, 
			$stat->cat_url
		);
		
		$toDate = strtotime($stat->first_published) > strtotime($this->firstlog) ? $stat->first_published : $this->firstlog;
		
		$yTickSize = max($stat->threemonth_views, $stat->twomonth_views, $stat->lastmonth_views, 
			$stat->thismonth_views, $stat->threemonth_primary, $stat->twomonth_primary, 
			$stat->lastmonth_primary, $stat->thismonth_primary);
		
		// Get user data from text log (primary content accesses - get unique users)
		//$users = $this->helper->getUserLogs($stat, $this->pubconfig, 'primary');
		//$userViews = $this->helper->getUserLogs($stat, $this->pubconfig, 'view');
		
		$i++;				
		?>
			<table class="pubstats-wrap">
				<tr><td colspan="6" class="pubstats-h">
					<img src="<?php echo $pubthumb; ?>" alt=""/>
					<span class="h-title"><a href="<?php echo JRoute::_('index.php?option=com_publications' . a . 'id=' . $stat->publication_id) . '?version=' . $stat->version_number; ?>"><?php echo $stat->title; ?></a></span>
					<span class="block mini faded"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLISHED') . ' ' . JHTML::_('date', $stat->published_up, $dateFormat, $tz) . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_IN') . ' ' . $stat->cat_name; ?></span>
				</td></tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td><?php 
						echo '<span class="pubstats-label">' . JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_THIS_MONTH') . '</span><span class="pubstats-note">' . $thisMonth . '</span>'; 
					?></td>
					
					<td><?php echo '<span class="pubstats-label">' . JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_LAST_MONTH') . '</span><span class="pubstats-note">' . $lastMonth . '</span>';  ?></td>
					
					<td><?php echo '<span class="pubstats-label"><span class="prominent">' . JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_TOTAL') . '</span>*</span><span class="pubstats-note">*' . JText::_('PLG_PROJECTS_PUBLICATIONS_SINCE') . ' ' .JHTML::_('date', $toDate, $dateFormat, $tz) . ' ' . '</span>';  ?></td>
					
				</tr>
				<tr>
					<td class="pubstats-sh"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_VIEWS'); ?> <?php if ($i == 1) { ?> <span class="info-pop tooltips" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_VIEWS_TIPS_TITLE_ABOUT'); ?>">&nbsp;</span> <?php } ?></td>
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
								      $('<div>' +  contents + append + '</div>').css( {
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
										for(var m = 0; m < points[k].data.length; m++)
										{
											if (points[k].data[m][0] != null && points[k].data[m][1] != null)
											{
										  		if (k == 0)
												{
											  		showTooltip(graphx + points[k].xaxis.p2c(points[k].data[m][0]) - 15, 
														graphy + points[k].yaxis.p2c(points[k].data[m][1]) + 10,
														points[k].data[m][1], append)
										  		}
												else
												{
											 		showTooltip(graphx + points[k].xaxis.p2c(points[k].data[m][0]) - 15, 
														graphy + points[k].yaxis.p2c(points[k].data[m][1]) - 45,
														points[k].data[m][1], append) 
										  		}

											}
										}
									}
								}
															
								var data = [[0, <?php echo $stat->threemonth_views; ?>], 
											[1, <?php echo $stat->twomonth_views; ?>], 
											[2, <?php echo $stat->lastmonth_views; ?>],
											[3, <?php echo $stat->thismonth_views; ?>]];
								
								var ph 			= $('#view-<?php echo $stat->publication_id; ?>');
								var xticks 		= [<?php echo $xticks; ?>];									
								var fillCol 	= "#f8e7b3";
								var yTickSize 	= <?php echo $yTickSize; ?>;
								var tipContent 	= '%y';
								
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
					<td class="pubstats-sh"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_ACCESSES'); ?> <?php if ($i == 1) { ?> <span class="info-pop tooltips" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_ACCESSES_TIPS_TITLE_ABOUT'); ?>">&nbsp;</span> <?php } ?></td>
					<td class="pubstats-graph"><div id="access-<?php echo $stat->publication_id; ?>" class="ph"></div>
					<script type="text/javascript">
						if (jQuery()) {
							var $ = jq;
														
							var data = [[0, <?php echo $stat->threemonth_primary; ?>], 
										[1, <?php echo $stat->twomonth_primary; ?>], 
										[2, <?php echo $stat->lastmonth_primary; ?>],
										[3, <?php echo $stat->thismonth_primary; ?>]];
							
							var ph 			= $('#access-<?php echo $stat->publication_id; ?>');
							var xticks 		= [<?php echo $xticks; ?>];									
							var fillCol 	= "#cdf0c1";
							var yTickSize 	= <?php echo $yTickSize; ?>;
							var tipContent 	= '%y';
							
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
	<p class="noresults"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATS_NO_INFO'); ?></p>
<?php } ?>
</div>
