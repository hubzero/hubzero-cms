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

$this->css('reports')
     ->css('impact.css', 'projects', 'publications');

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

<script src="<?php echo Request::base(true); ?>/core/assets/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo Request::base(true); ?>/core/assets/js/flot/jquery.flot.time.min.js"></script>
<script src="<?php echo Request::base(true); ?>/core/assets/js/flot/jquery.flot.pie.min.js"></script>
<script src="<?php echo Request::base(true); ?>/core/assets/js/flot/jquery.flot.resize.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo Request::base(true); ?>/core/assets/js/excanvas/excanvas.min.js"></script><![endif]-->

<header id="content-header" class="reports">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section" id="project-stats">
	<?php
		// Display status message
		$this->view('_statusmsg', 'projects')
		     ->set('error', $this->getError())
		     ->set('msg', $this->msg)
		     ->display();
	?>
	<?php if (empty($this->stats)) { ?>
		<p class="error"><?php echo Lang::txt('Statistics unavailable'); ?></p>
</section>
	<?php return; } ?>
	<table class="stats-wrap">
		<tr class="stats-general">
			<th class="stats-h" rowspan="2"><span><?php echo Lang::txt('Overview'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo Lang::txt('New projects'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo Lang::txt('More breakdown'); ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['general']['total']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('Total projects'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['general']['setup']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('Projects in setup'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['general']['active']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('Active projects'); ?></span></td>
			<?php if ($this->monthly) {

				$y         = 0;
				$xdata     = '';
				$xticks    = '';
				$yTickSize = $this->stats['general']['new'];

				foreach ($this->monthly as $month => $data)
				{
					$xdata  .= '[' . $y . ', ' . $data['general']['new'] . ']';
					$xdata  .= (($y + 1) == count($this->monthly)) ? '' : ',';
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
								for (var m = 0; m < points[k].data.length; m++)
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

						var data       = [<?php echo $xdata; ?>];
						var xticks     = [<?php echo $xticks; ?>];
						var ph         = $('#stat-total');
						var tipContent = '%y';
						var yTickSize  = <?php echo $yTickSize; ?>;

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
						<?php echo Lang::txt('new projects this month'); ?></li>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['public']; ?></span>
						<?php echo Lang::txt('public projects'); ?></li>
					<?php if ($this->config->get('grantinfo', 0)) { ?>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['sponsored']; ?></span>
					<?php echo Lang::txt('grant-sponsored projects'); ?></li>
					<?php } ?>
					<?php if ($this->config->get('restricted_data', 0)) { ?>
					<li><span class="stats-num-small"><?php echo $this->stats['general']['sensitive']; ?></span>
						<?php echo Lang::txt('projects with sensitive data'); ?></li>
					<?php } ?>
				</ul>
			</td>
		</tr>
	</table>

	<table class="stats-wrap">
		<tr class="stats-activity">
			<th class="stats-h" rowspan="2"><span><?php echo Lang::txt('Activity'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo Lang::txt('Active projects'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo Lang::txt('Top active projects'); ?>

			<?php if (!$this->admin) { echo '(' . Lang::txt('public') . ')'; } ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['activity']['total']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('total activity records'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['activity']['average']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('average activity records per project'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['activity']['usage']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('projects active in past 30 days'); ?></span></td>
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

								var data       = [<?php echo $xdata; ?>];
								var xticks     = [<?php echo $xticks; ?>];
								var ph         = $('#stat-activity');
								var tipContent = '%y%';
								var yTickSize  = <?php echo $yTickSize; ?>;

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
						?>
					<li><span class="stats-ima-small"><img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $topProject->alias . '&task=media'); ?>" alt="" /></span>
						<?php if (!$topProject->private) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=view&alias=' . $topProject->alias); ?>"> <?php } ?>
						<?php echo $topProject->title; ?><?php if (!$topProject->private) { ?></a> <?php } ?>
					</li>
					<?php } ?>
				</ul>
				<?php } else { ?>
					<p class="noresults"><?php echo Lang::txt('Detailed information currently unavailable'); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>

	<table class="stats-wrap">
		<tr class="stats-team">
			<th class="stats-h" rowspan="2"><span><?php echo Lang::txt('Team'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo Lang::txt('New team members added'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo Lang::txt('Top biggest team projects'); ?>
			<?php if (!$this->admin) { echo '(' . Lang::txt('public') . ')'; } ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['team']['total']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('total members in all teams'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['team']['average']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('average project team size'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['team']['multi']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('projects have multi-person teams'); ?></span></td>
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
					<?php foreach ($this->stats['topTeamProjects'] as $topProject) {?>
					<li><span class="stats-ima-small"><img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $topProject->alias . '&task=media'); ?>" alt="" /></span>
						<?php if (!$topProject->private) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=view&alias=' . $topProject->alias); ?>"> <?php } ?>
						<?php echo $topProject->title . ' (' . $topProject->team . ' ' . Lang::txt('members') . ')'; ?><?php if (!$topProject->private) { ?></a> <?php } ?>
					</li>
					<?php } ?>
				</ul>
				<span class="block">&nbsp;</span>
				<ul>
					<li><span class="stats-num-small"><?php echo $this->stats['team']['multiusers']; ?></span>
						<?php echo Lang::txt('unique users with multiple projects'); ?></li>
				</ul>
				<?php } else { ?>
					<p class="noresults"><?php echo Lang::txt('Detailed information currently unavailable'); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>

	<table class="stats-wrap">
		<tr class="stats-files">
			<th class="stats-h" rowspan="2"><span><?php echo Lang::txt('Files'); ?>
				<?php if (isset($this->stats['updated'])) { echo '*'; } ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo Lang::txt('Total files stored'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo Lang::txt('More stats'); ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['files']['total']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('files stored'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['files']['average']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('average files per project'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['files']['usage']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('projects store files'); ?></span></td>
					<?php if ($this->monthly) {

						$y         = 0;
						$xdata     = '';
						$xticks    = '';
						$yTickSize = $this->stats['files']['total'];

						foreach ($this->monthly as $month => $data)
						{
							$xdata  .= '[' . $y . ', ' . $data['files']['total'] . ']';
							$xdata  .= (($y + 1) == count($this->monthly)) ? '' : ',';
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

								var data       = [<?php echo $xdata; ?>];
								var xticks     = [<?php echo $xticks; ?>];
								var ph         = $('#stat-files');
								var tipContent = '%y';
								var yTickSize  = <?php echo $yTickSize; ?>;

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
					<?php echo Lang::txt('total Git commits'); ?></li>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['files']['diskspace']; ?></span>
					<?php echo Lang::txt('total used disk space'); ?></li>
				</ul>
			</td>
		</tr>
	</table>
	<?php if ($this->publishing) { ?>
	<table class="stats-wrap">
		<tr class="stats-publications">
			<th class="stats-h" rowspan="2"><span><?php echo Lang::txt('Publications'); ?></span></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if ($this->monthly) { ?>
			<th class="stats-graph"><?php echo Lang::txt('Publication releases'); ?></th>
			<?php } ?>
			<th class="stats-more"><?php echo Lang::txt('More stats'); ?></th>
		</tr>

		<tr>
			<td><span class="stats-num"><?php echo $this->stats['pub']['total']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('publications started'); ?></span> </td>
			<td><span class="stats-num"><?php echo $this->stats['pub']['average']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('average publications per project'); ?></span></td>
			<td><span class="stats-num"><?php echo $this->stats['pub']['usage']; ?></span>
				<span class="stats-label"><?php echo Lang::txt('projects have used publications'); ?></span></td>
					<?php if ($this->monthly) {

						$y         = 0;
						$xdata     = '';
						$xticks    = '';
						$yTickSize = round($this->stats['pub']['total']/$this->stats['general']['total'], 0);

						foreach ($this->monthly as $month => $data)
						{
							$xdata  .= '[' . $y . ', ' . $data['pub']['new'] . ']';
							$xdata  .= (($y + 1) == count($this->monthly)) ? '' : ',';
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

								var data       = [<?php echo $xdata; ?>];
								var xticks     = [<?php echo $xticks; ?>];
								var ph         = $('#stat-pub');
								var tipContent = '%y';
								var yTickSize  = <?php echo $yTickSize; ?>;

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
					<?php echo Lang::txt('publicly released publications'); ?></li>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['pub']['versions']; ?></span>
					<?php echo Lang::txt('total publication versions'); ?></li>
					<li><span class="stats-num-small-unfloat"><?php echo $this->stats['files']['pubspace'] ? $this->stats['files']['pubspace'] : 'N/A'; ?></span>
					<?php echo Lang::txt('allocated to published files'); ?></li>
				</ul>
			</td>
		</tr>
	</table>
	<?php } ?>
	<?php if (isset($this->stats['updated'])) { echo '<p>*Last updated ' . $this->stats['updated'] . '</p>'; } ?>
</section>