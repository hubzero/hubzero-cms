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

// Common options for js charts
$options = "
xaxis: { ticks: xticks },
yaxis: { ticks: [[0, ''], [yTickSize, yTickSize]], color: 'transparent', tickDecimals:0, labelWidth: 0 },
series: {
	lines: { 
		show: true,
		fill: true
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

<script src="/media/system/js/flot/jquery.flot.min.js"></script>
<script src="/media/system/js/flot/jquery.flot.tooltip.min.js"></script>
<script src="/media/system/js/flot/jquery.flot.pie.min.js"></script>
<script src="/media/system/js/flot/jquery.flot.resize.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/media/system/js/excanvas/excanvas.min.js"></script><![endif]-->

<div id="content-header" class="reports">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section" id="project-stats">
	<div class="status-msg">
	<?php 
		// Display error or success message
		if ($this->getError()) { 
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>

	<table class="stats-wrap">
		<tr class="stats-general">
			<th class="stats-h" rowspan="2"><span><?php echo JText::_('Overview'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo JText::_('New projects'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo JText::_('More breakdown'); ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['general']['total']; ?></span> 
				<span class="stats-label"><?php echo JText::_('Total projects'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['general']['setup']; ?></span> 
				<span class="stats-label"><?php echo JText::_('Projects in setup'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['general']['active']; ?></span> 
				<span class="stats-label"><?php echo JText::_('Active projects'); ?></span></td>
			<?php if ($this->monthly) { 
				
				$y 			= 0;
				$xdata 		= '';
				$xticks 	= '';
				$yTickSize 	= $this->stats['general']['new'];
				
				foreach ($this->monthly as $month => $data)
				{
					$xdata 	.= '[' . $y . ', ' . $data['general']['new'] . ']';
					$xdata 	.= (($y + 1) == count($this->monthly)) ? '' : ',';
					$xticks .= "[" . $y . ", '" . $month . "']";
					$xticks .= (($y + 1) == count($this->monthly)) ? '' : ',';
					$y++;
				}
			?>
			<td class="stats-graph">			
				<div id="stat-total" class="ph"></div>
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

						var data   		= [<?php echo $xdata; ?>];
						var xticks 		= [<?php echo $xticks; ?>];																			
						var ph 	   		= $('#stat-total');
						var tipContent 	= '%y';
						var yTickSize 	= <?php echo $yTickSize; ?>;
						
						if (ph.length > 0)
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
			<?php } ?>
			<td class="stats-more">
				<ul>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['new']; ?></span> 
						<?php echo JText::_('new projects this month'); ?></li>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['public']; ?></span> 
						<?php echo JText::_('public projects'); ?></li>
					<?php if($this->config->get('grantinfo', 0)) { ?>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['sponsored']; ?></span> 
					<?php echo JText::_('grant-sponsored projects'); ?></li>
					<?php } ?>
					<?php if($this->config->get('restricted_data', 0)) { ?>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['sensitive']; ?></span> 
						<?php echo JText::_('projects with sensitive data'); ?></li>
					<?php } ?>
				</ul>
			</td>
		</tr>
	</table>
	
	<table class="stats-wrap">
		<tr class="stats-activity">
			<th class="stats-h" rowspan="2"><span><?php echo JText::_('Activity'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo JText::_('Active projects'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo JText::_('Top active projects'); ?>
				
			<?php if (!$this->admin) { echo '(' . JText::_('public') . ')'; } ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['activity']['total']; ?></span> 
				<span class="stats-label"><?php echo JText::_('total activity records'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['activity']['average']; ?></span> 
				<span class="stats-label"><?php echo JText::_('average activity records per project'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['activity']['usage']; ?></span> 
				<span class="stats-label"><?php echo JText::_('projects active in past 30 days'); ?></span></td>
					<?php if ($this->monthly) { 

						$y 			= 0;
						$xdata 		= '';
						$xticks 	= '';
						$yTickSize 	= str_replace('%', '', $this->stats['activity']['usage']);

						foreach ($this->monthly as $month => $data)
						{
							$xdata 	.= '[' . $y . ', ' . str_replace('%', '', $data['activity']['usage']) . ']';
							$xdata 	.= (($y + 1) == count($this->monthly)) ? '' : ',';
							$xticks .= "[" . $y . ", '" . $month . "']";
							$xticks .= (($y + 1) == count($this->monthly)) ? '' : ',';
							$y++;
						}
					?>
					<td class="stats-graph">			
						<div id="stat-activity" class="ph"></div>
						<script type="text/javascript">
							if (jQuery()) {
								var $ = jq;

								var data   		= [<?php echo $xdata; ?>];
								var xticks 		= [<?php echo $xticks; ?>];																			
								var ph 	   		= $('#stat-activity');
								var tipContent 	= '%y%';
								var yTickSize 	= <?php echo $yTickSize; ?>;

								if (ph.length > 0)
								{
									var chart = $.plot( ph, [data], {								
										<?php echo $options; ?>
									}); 

									// Show labels in Safari
									if (safari)
									{
										var points = chart.getData();
										showLabels(ph, points, '%');
									}
								}				
							}
						</script>	
					</td>
					<?php } ?>
			<td class="stats-more">
				<?php if (!empty($this->stats['topActiveProjects'])) { ?>
				<ul>
					<?php foreach ($this->stats['topActiveProjects'] as $topProject) { 
						$thumb = ProjectsHtml::getThumbSrc($topProject->id, $topProject->alias, $topProject->picture, $this->config);
						?>
					<li><span class="stats-ima-small"><img src="<?php echo $thumb; ?>" alt="" /></span>
						<?php if (!$topProject->private) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'task=view' . a . 'alias=' . $topProject->alias); ?>"> <?php } ?>
						<?php echo $topProject->title; ?><?php if (!$topProject->private) { ?></a> <?php } ?>
					</li>
					<?php } ?>
				</ul>
				<?php } else { ?>
					<p class="noresults"><?php echo JText::_('Detailed information currently unavailable'); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>
	
	<table class="stats-wrap">
		<tr class="stats-team">
			<th class="stats-h" rowspan="2"><span><?php echo JText::_('Team'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo JText::_('New team members added'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo JText::_('Top biggest team projects'); ?>
			<?php if (!$this->admin) { echo '(' . JText::_('public') . ')'; } ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['team']['total']; ?></span> 
				<span class="stats-label"><?php echo JText::_('total members in all teams'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['team']['average']; ?></span> 
				<span class="stats-label"><?php echo JText::_('average project team size'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['team']['multi']; ?></span> 
				<span class="stats-label"><?php echo JText::_('projects have multi-person teams'); ?></span></td>
					<?php if ($this->monthly) { 

						$y 			= 0;
						$xdata 		= '';
						$xticks 	= '';
						$yTickSize 	= round($this->stats['team']['total']/$this->stats['general']['total'], 0);

						foreach ($this->monthly as $month => $data)
						{
							$xdata 	.= '[' . $y . ', ' . $data['team']['new'] . ']';
							$xdata 	.= (($y + 1) == count($this->monthly)) ? '' : ',';
							$xticks .= "[" . $y . ", '" . $month . "']";
							$xticks .= (($y + 1) == count($this->monthly)) ? '' : ',';
							$y++;
						}
					?>
					<td class="stats-graph">				
						<div id="stat-team" class="ph"></div>
						<script type="text/javascript">
							if (jQuery()) {
								var $ = jq;

								var data   		= [<?php echo $xdata; ?>];
								var xticks 		= [<?php echo $xticks; ?>];																			
								var ph 	   		= $('#stat-team');
								var tipContent 	= '%y';
								var yTickSize 	= <?php echo $yTickSize; ?>;

								if (ph.length > 0)
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
					<?php } ?>
			<td class="stats-more">
				<?php if (!empty($this->stats['topTeamProjects'])) { ?>
				<ul>
					<?php foreach ($this->stats['topTeamProjects'] as $topProject) { 
						$thumb = ProjectsHtml::getThumbSrc($topProject->id, $topProject->alias, $topProject->picture, $this->config);
						?>
					<li><span class="stats-ima-small"><img src="<?php echo $thumb; ?>" alt="" /></span>
						<?php if (!$topProject->private) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'task=view' . a . 'alias=' . $topProject->alias); ?>"> <?php } ?>
						<?php echo $topProject->title . ' (' . $topProject->team . ' ' . JText::_('members') . ')'; ?><?php if (!$topProject->private) { ?></a> <?php } ?>
					</li>
					<?php } ?>
				</ul>
				<span class="block">&nbsp;</span>
				<ul>
					<li><span class="stats-num-small"><?php echo $this->stats['team']['multiusers']; ?></span> 
						<?php echo JText::_('unique users with multiple projects'); ?></li>
				</ul>
				<?php } else { ?>
					<p class="noresults"><?php echo JText::_('Detailed information currently unavailable'); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>
	
	<table class="stats-wrap">
		<tr class="stats-files">
			<th class="stats-h" rowspan="2"><span><?php echo JText::_('Files'); ?>
				<?php if (isset($this->stats['updated'])) { echo '*'; } ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo JText::_('Total files stored'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo JText::_('More stats'); ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['files']['total']; ?></span> 
				<span class="stats-label"><?php echo JText::_('files stored'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['files']['average']; ?></span> 
				<span class="stats-label"><?php echo JText::_('average files per project'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['files']['usage']; ?></span> 
				<span class="stats-label"><?php echo JText::_('projects store files'); ?></span></td>
					<?php if ($this->monthly) { 

						$y 			= 0;
						$xdata 		= '';
						$xticks 	= '';
						$yTickSize 	= $this->stats['files']['total'];

						foreach ($this->monthly as $month => $data)
						{
							$xdata 	.= '[' . $y . ', ' . $data['files']['total'] . ']';
							$xdata 	.= (($y + 1) == count($this->monthly)) ? '' : ',';
							$xticks .= "[" . $y . ", '" . $month . "']";
							$xticks .= (($y + 1) == count($this->monthly)) ? '' : ',';
							$y++;
						}
					?>
					<td class="stats-graph">				
						<div id="stat-files" class="ph"></div>
						<script type="text/javascript">
							if (jQuery()) {
								var $ = jq;

								var data   		= [<?php echo $xdata; ?>];
								var xticks 		= [<?php echo $xticks; ?>];																			
								var ph 	   		= $('#stat-files');
								var tipContent 	= '%y';
								var yTickSize 	= <?php echo $yTickSize; ?>;

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
					<?php } ?>
			<td class="stats-more">
				<ul>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['files']['commits']; ?></span> 
					<?php echo JText::_('total Git commits'); ?></li>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['files']['diskspace']; ?></span> 
					<?php echo JText::_('total used disk space'); ?></li>
				</ul>
			</td>
		</tr>
	</table>
	<?php if ($this->publishing) { ?>
	<table class="stats-wrap">
		<tr class="stats-publications">
			<th class="stats-h" rowspan="2"><span><?php echo JText::_('Publications'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo JText::_('Publication releases'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo JText::_('More stats'); ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['pub']['total']; ?></span> 
				<span class="stats-label"><?php echo JText::_('publications started'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['pub']['average']; ?></span> 
				<span class="stats-label"><?php echo JText::_('average publications per project'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['pub']['usage']; ?></span> 
				<span class="stats-label"><?php echo JText::_('projects have used publications'); ?></span></td>
					<?php if ($this->monthly) { 

						$y 			= 0;
						$xdata 		= '';
						$xticks 	= '';
						$yTickSize 	= round($this->stats['pub']['total']/$this->stats['general']['total'], 0);

						foreach ($this->monthly as $month => $data)
						{
							$xdata 	.= '[' . $y . ', ' . $data['pub']['new'] . ']';
							$xdata 	.= (($y + 1) == count($this->monthly)) ? '' : ',';
							$xticks .= "[" . $y . ", '" . $month . "']";
							$xticks .= (($y + 1) == count($this->monthly)) ? '' : ',';
							$y++;
						}
					?>
					<td class="stats-graph">				
						<div id="stat-pub" class="ph"></div>
						<script type="text/javascript">

							if (jQuery()) {
								var $ = jq;

								var data   		= [<?php echo $xdata; ?>];
								var xticks 		= [<?php echo $xticks; ?>];																			
								var ph 	   		= $('#stat-pub');
								var tipContent 	= '%y';
								var yTickSize 	= <?php echo $yTickSize; ?>;

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
					<?php } ?>
			<td class="stats-more">
				<ul>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['pub']['released']; ?></span> 
					<?php echo JText::_('publicly released publications'); ?></li>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['pub']['versions']; ?></span> 
					<?php echo JText::_('total publication versions'); ?></li>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['files']['pubspace'] ? $this->stats['files']['pubspace'] : 'N/A'; ?></span> 
					<?php echo JText::_('allocated to published files'); ?></li>
				</ul>
			</td>
		</tr>
	</table>
	<?php } ?>
	<?php if (isset($this->stats['updated'])) { echo '<p>*Last updated ' . $this->stats['updated'] . '</p>'; } ?>
</div>