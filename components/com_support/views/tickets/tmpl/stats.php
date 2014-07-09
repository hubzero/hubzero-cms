<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @author	Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();

$base = rtrim(JURI::getInstance()->base(true), '/');

$database = JFactory::getDBO();
$sql = "SELECT resolved
		FROM #__support_tickets
		WHERE open=0
		AND type='{$this->type}' ";
		if ($this->group == '_none_')
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else if ($this->group)
		{
			$sql .= " AND `group`='{$this->group}' ";
		}
		$sql .= " ORDER BY resolved ASC";
$database->setQuery($sql);
$resolutions = $database->loadObjectList();

$total = count($resolutions);
$res = array();
foreach ($resolutions as $resolution)
{
	if (!isset($res[$resolution->resolved]))
	{
		$res[$resolution->resolved] = 1;
	}
	else
	{
		$res[$resolution->resolved]++;
	}
}

$sql = "SELECT severity
		FROM #__support_tickets
		WHERE type='{$this->type}' ";
		if ($this->group == '_none_')
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else if ($this->group)
		{
			$sql .= " AND `group`='{$this->group}' ";
		}
		$sql .= " ORDER BY severity ASC";
$database->setQuery($sql);
$severities = $database->loadObjectList();

$total = count($severities);
$sev = array();
foreach ($severities as $severity)
{
	if (!isset($sev[$severity->severity]))
	{
		$sev[$severity->severity] = 1;
	}
	else
	{
		$sev[$severity->severity]++;
	}
}

function getMonthName($month)
{
	$monthname = '';
	switch (intval($month))
	{
		case 1: $monthname = JText::_('January');   break;
		case 2: $monthname = JText::_('February');  break;
		case 3: $monthname = JText::_('March');     break;
		case 4: $monthname = JText::_('April');     break;
		case 5: $monthname = JText::_('May');       break;
		case 6: $monthname = JText::_('June');      break;
		case 7: $monthname = JText::_('July');      break;
		case 8: $monthname = JText::_('August');    break;
		case 9: $monthname = JText::_('September'); break;
		case 0: $monthname = JText::_('October');   break;
		case 11: $monthname = JText::_('November');  break;
		case 12: $monthname = JText::_('December');  break;
	}
	return $monthname;
}
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-browse browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>">
					<?php echo JText::_('COM_SUPPORT_TICKETS'); ?>
				</a>
			</li>
			<li class="last">
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>">
					<?php echo JText::_('COM_SUPPORT_NEW_TICKET'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<ul class="sub-menu">
	<li id="sm-1"<?php if ($this->type == 0) { echo ' class="active"'; } ?>>
		<a class="tab" rel="submitted" href="<?php echo JRoute::_('index.php?option=com_support&task=stats'); ?>">
			<span><?php echo JText::_('COM_SUPPORT_TICKETS_SUBMITTED'); ?></span>
		</a>
	</li>
	<li id="sm-2"<?php if ($this->type == 1) { echo ' class="active"'; } ?>>
		<a class="tab" rel="automatic" href="<?php echo JRoute::_('index.php?option=com_support&task=stats&type=automatic'); ?>">
			<span><?php echo JText::_('COM_SUPPORT_TICKETS_AUTOMATIC'); ?></span>
		</a>
	</li>
</ul>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>" method="get" enctype="multipart/form-data">
	<section class="main section" id="ticket-stats">
		<div class="grid">
			<div class="col span-half">
				<p class="time-range">
					<label for="start-date"><?php echo JText::_('COM_SUPPORT_DATE_FROM'); ?></label> <input type="text" name="start" id="start-date" value="<?php echo $this->escape($this->start); ?>" size="7" />
					<label for="end-date"><?php echo JText::_('COM_SUPPORT_DATE_TO'); ?></label> <input type="text" name="end" id="end-date" value="<?php echo $this->escape($this->end); ?>" size="7" />
				</p>
			</div><!-- / .col span-half omega -->
			<div class="col span-half omega">
				<fieldset class="support-stats-filter">
					<label for="ticket-group">
						<?php echo JText::_('COM_SUPPORT_FILTER_GROUP'); ?>
					</label>
					<select name="group" id="ticket-group">
						<option value=""<?php if (!$this->group) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_ALL'); ?></option>
						<option value="_none_"<?php if ($this->group == '_none_') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_NONE'); ?></option>
						<?php
						if ($this->groups)
						{
							foreach ($this->groups as $group)
							{
						?>
						<option value="<?php echo $group->group; ?>"<?php if ($this->group == $group->group) { echo ' selected="selected"'; } ?>><?php echo ($group->description) ? stripslashes($this->escape($group->description)) : $this->escape($group->group); ?></option>
						<?php
							}
						}
						?>
					</select>
					<input type="submit" value="Go" />
				</fieldset>
			</div><!-- / .col span-half omega -->
		</div><!-- / .grid -->

		<div class="container">
			<div id="container" style="min-width: 400px; height: 200px; margin: 0 auto;"></div>
			<?php
				$top = 0;

				$closeddata = '';
				if ($this->closedmonths)
				{
					$c = array();
					foreach ($this->closedmonths as $year => $data)
					{
						foreach ($data as $k => $v)
						{
							$top = ($v > $top) ? $v : $top;
							$c[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
						}
					}
					$closeddata = implode(',', $c);
				}

				$openeddata = '';
				if ($this->openedmonths)
				{
					$o = array();
					foreach ($this->openedmonths as $year => $data)
					{
						foreach ($data as $k => $v)
						{
							$top = ($v > $top) ? $v : $top;
							$o[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
						}
					}
					$openeddata = implode(',', $o);
				}

				$this->js('flot/jquery.flot.min.js', 'system')
				     ->js('flot/jquery.flot.tooltip.min.js', 'system')
				     ->js('flot/jquery.flot.pie.min.js', 'system')
				     ->js('flot/jquery.flot.resize.js', 'system');
			?>
			<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo $base; ?>/media/system/js/excanvas/excanvas.min.js"></script><![endif]-->
			<script type="text/javascript">
				if (!jq) {
					var jq = $;
				}
				if (jQuery()) {
					var $ = jq,
						chart,
						month_short = [
							'<?php echo JText::_('JANUARY_SHORT'); ?>',
							'<?php echo JText::_('FEBRUARY_SHORT'); ?>',
							'<?php echo JText::_('MARCH_SHORT'); ?>',
							'<?php echo JText::_('APRIL_SHORT'); ?>',
							'<?php echo JText::_('MAY_SHORT'); ?>',
							'<?php echo JText::_('JUNE_SHORT'); ?>',
							'<?php echo JText::_('JULY_SHORT'); ?>',
							'<?php echo JText::_('AUGUST_SHORT'); ?>',
							'<?php echo JText::_('SEPTEMBER_SHORT'); ?>',
							'<?php echo JText::_('OCTOBER_SHORT'); ?>',
							'<?php echo JText::_('NOVEMBER_SHORT'); ?>',
							'<?php echo JText::_('DECEMBER_SHORT'); ?>'
						],
						datasets = [
							{
								color: "#AA4643", //#93ACCA
								label: "<?php echo JText::_('COM_SUPPORT_OPENED'); ?>",
								data: [<?php echo $openeddata; ?>]
							},
							{
								color: "#656565", //#CFCFAB
								label: "<?php echo JText::_('COM_SUPPORT_CLOSED'); ?>",
								data: [<?php echo $closeddata; ?>]
							}
						];

					$(document).ready(function() {
						var chart = $.plot($('#container'), datasets, {
							series: {
								lines: {
									show: true,
									fill: true
								},
								points: { show: false },
								shadowSize: 0
							},
							//crosshair: { mode: "x" },
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
								show: true,
								noColumns: 2,
								position: "nw",
								margin: [50, 5]
							},
							xaxis: { mode: "time", tickLength: 0, tickDecimals: 0, <?php if (count($o) <= 12) { echo 'ticks: ' . count($o) . ','; } ?>
								tickFormatter: function (val, axis) {
									var d = new Date(val);
									return month_short[d.getUTCMonth()] + ' ' + d.getFullYear();
								}
							},
							yaxis: { min: 0 }
						});
					});
				}
			</script>
		</div><!-- / #container -->

		<div class="container breakdown">
			<table class="support-stats-overview">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_OPENED'); ?></th>
						<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_CLOSED'); ?></th>
						<th scope="col" class="block"><?php echo JText::_('COM_SUPPORT_STATS_AVERAGE_LIFETIME'); ?></th>
						<th scope="col" class="major"><?php echo JText::_('COM_SUPPORT_STATS_UNASSIGNED'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo $this->opened['open']; ?></td>
						<td><?php echo $this->opened['closed']; ?></td>
						<td class="block">
							<?php
							$lifetime = SupportUtilities::calculateAverageLife($this->closedTickets);
							?>
							<?php echo (isset($lifetime[0])) ? $lifetime[0] : 0; ?> <span><?php echo JText::_('COM_SUPPORT_STATS_DAYS'); ?></span>
							<?php echo (isset($lifetime[1])) ? $lifetime[1] : 0; ?> <span><?php echo JText::_('COM_SUPPORT_STATS_HOURS'); ?></span>
							<?php echo (isset($lifetime[2])) ? $lifetime[2] : 0; ?> <span><?php echo JText::_('COM_SUPPORT_STATS_MINUTES'); ?></span>
						</td>
						<td class="major"><?php echo $this->opened['unassigned']; ?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- / .container breakdown -->

	<div class="container breakdown pies">
		<div class="grid">
			<div class="col span-half">
				<h3><?php echo JText::_('COM_SUPPORT_TICKETS_BY_SEVERITY'); ?></h3>
				<div id="severities-container" style="min-width: 270px; height: 270px;">
					<table class="support-stats-resolutions">
						<thead>
							<tr>
								<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_SEVERITY'); ?></th>
								<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_NUMBER'); ?></th>
								<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_PERCENT'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$colors = array(
									'#656565',
									'#7c94c2', //'#7c7c7c',
									'#c67c6b', //'#515151',
									'#d8aa65', //'#404040',//'#d9d9d9',
									'#5f9c63', //'#3d3d3d',
									'#9b569b', //'#797979',
									'#5ca1b6', //'#595959',
									'#ce89a0', //'#e5e5e5',
									'#86a558', //'#828282',
									'#b57676', //'#404040',
									'#738aa0', //'#6a6a6a',
									'#dfe6ef', //'#7c7c7c',
									'#93ACCA', //'#515151',
									'#83ae92', //'#404040',//'#d9d9d9',
									'#4a6f81', //'#3d3d3d',
									'#dfbd5b', //'#797979',
									'#e88f87', //'#595959',
									'#CFCFAB', //'#e5e5e5',
									'#598ba4', //'#828282',
									'#82b5c6', //'#404040',
									'#99B1A5' //'#6a6a6a',
								);

								$severities = SupportUtilities::getSeverities($this->config->get('severities'));

								$cls = 'odd';
								$data = array();
								$i = 0;

								$severtes = array();
								foreach ($severities as $k => $severity)
								{
									$key = (isset($sev[$severity])) ? (string) $sev[$severity] : '0';
									if (isset($severtes[$key]))
									{
										$key .= '.' . $k;
									}
									$severtes[$key] = $severity;
								}
								krsort($severtes);

								foreach ($severtes as $severity)
								{
									$r  = "{label: '" . $this->escape(addslashes($severity)) . "', data: ";
									$r .= (isset($sev[$severity])) ? round(($sev[$severity]/$total)*100, 2) : 0;
									$r .= ", color: '" . $colors[$i] . "'}";

									$data[] = $r;

									$cls = ($cls == 'even') ? 'odd' : 'even';
							?>
							<tr class="<?php echo $cls; ?>">
								<th scope="row"><?php echo $this->escape(stripslashes($severity)); ?></th>
								<td><?php echo (isset($sev[$severity])) ? $sev[$severity] : '0'; ?></td>
								<td><?php echo (isset($sev[$severity])) ? round($sev[$severity]/$total*100, 2) : '0'; ?></td>
							</tr>
							<?php
									$i++;
								}
							?>
						</tbody>
					</table>
				</div><!-- / #severities-container -->
				<script type="text/javascript">
				if (jQuery()) {
					var $ = jq, severityPie;

					function labelFormatter(label, series) {
						return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
					}

					$(document).ready(function() {
						severityPie = $.plot($("#severities-container"), [<?php echo implode(',' . "\n", $data); ?>], {
							series: {
								pie: {
									radius: 1,
									label: {
										show: true,
										radius: 2/3,
										formatter: labelFormatter,
										threshold: 0.03
									},
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
					});
				}
				</script>
			</div><!-- / .col span-half -->
			<div class="col span-half omega">
				<h3><?php echo JText::_('COM_SUPPORT_TICKETS_BY_RESOLUTION'); ?></h3>
				<div id="resolutions-container" style="min-width: 270px; height: 270px;">
					<table class="support-stats-resolutions">
						<thead>
							<tr>
								<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_RESOLUTION'); ?></th>
								<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_NUMBER'); ?></th>
								<th scope="col"><?php echo JText::_('COM_SUPPORT_STATS_PERCENT'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr class="odd">
								<th scope="row"><?php echo JText::_('COM_SUPPORT_STATS_RESOLUTION_NONE'); ?></th>
								<td><?php echo (isset($res['noresolution'])) ? $res['noresolution'] : '0'; ?></td>
								<td><?php echo (isset($res['noresolution'])) ? $res['noresolution']/$total : '0'; ?></td>
							</tr>
						<?php
							$sr = new SupportResolution($database);
							$resolutions = $sr->getResolutions();

							$cls = 'odd';

							$i = 0;
							$data = array();

							$resolutns = array();
							foreach ($resolutions as $k => $resolution)
							{
								$key = (isset($res[$resolution->alias])) ? (string) $res[$resolution->alias] : '0';
								if (isset($resolutns[$key]))
								{
									$key .= '.' . $k;
								}
								$resolutns[$key] = $resolution;
							}
							krsort($resolutns);

							foreach ($resolutns as $resolution)
							{
								$r  = "{label: '" . $this->escape(addslashes($resolution->title)) . "', data: ";
								$r .= (isset($res[$resolution->alias])) ? round(($res[$resolution->alias]/$total)*100, 2) : 0;
								$r .= ", color: '" . $colors[$i] . "'}";
								$data[] = $r;

								$cls = ($cls == 'even') ? 'odd' : 'even';
						?>
							<tr class="<?php echo $cls; ?>">
								<th scope="row"><?php echo $this->escape(stripslashes($resolution->title)); ?></th>
								<td><?php echo (isset($res[$resolution->alias])) ? $res[$resolution->alias] : '0'; ?></td>
								<td><?php echo (isset($res[$resolution->alias])) ? round($res[$resolution->alias]/$total*100, 2) : '0'; ?></td>
							</tr>
						<?php
								$i++;
							}
							$nores = "{label: '" . JText::_('COM_SUPPORT_STATS_RESOLUTION_NONE') . "', data: " . (isset($res['noresolution']) ? $res['noresolution']/$total : '0') . ", color: '" . $colors[$i] . "'}";
							array_push($data, $nores);
						?>
						</tbody>
					</table>
				</div><!-- / #resolutions-container -->
				<script type="text/javascript">
				if (jQuery()) {
					var $ = jq, resolutionPie;
					$(document).ready(function() {
						resolutionPie = $.plot($("#resolutions-container"), [<?php echo implode(',' . "\n", $data); ?>], {
							series: {
								pie: {
									radius: 1,
									label: {
										show: true,
										radius: 2/3,
										formatter: labelFormatter,
										threshold: 0.03
									},
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
				    });
				}
				</script>
			</div><!-- / .col span-half omega -->
		</div><!-- / .grid -->
	</div><!-- / .container -->

	<div class="grid">
	<?php
	if ($this->users)
	{
		$chunked = array_chunk($this->users, 2);

		$z = 0;
		$j = 1;
		foreach ($chunked as $chunked)
		{
			foreach ($chunked as $user)
			{
				if ($z == 1)
				{
					?>
					</div><!-- / .col span-half -->
					<div class="col span-half omega">
					<?php
				}
				else if ($z == 2)
				{
					$z = 0;
					?>
					</div><!-- / .col span-half -->
				</div><!-- / .grid -->
				<div class="grid">
					<div class="col span-half">
					<?php
				}
				else
				{
					?>
					<div class="col span-half">
					<?php
				}

				$closeddata = '';
				//$utot = 0;
				if ($user->closed)
				{
					$c = array();
					foreach ($user->closed as $year => $data)
					{
						foreach ($data as $k => $v)
						{
							//$utot += $v;
							$c[] = '[Date.UTC(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
						}
					}
					$closeddata = implode(',', $c);
				}
				$anon = 0;
				$profile = \Hubzero\User\Profile::getInstance($user->id);
				if (!$profile)
				{
					$anon = 1;
				}
	?>
	<div class="breakdown container">
		<div class="entry-head">
			<p class="entry-rank">
				<strong>#<?php echo $j; ?></strong>
			</p>
			<p class="entry-member-photo">
				<img src="<?php echo $profile->getPicture($anon); ?>" alt="<?php echo $this->escape(stripslashes($user->name)); ?>" />
			</p>
			<p class="entry-title">
				<?php echo $this->escape(stripslashes($user->name)); ?><br />
				<span><?php echo JText::sprintf('COM_SUPPORT_STATS_NUM_ASSIGNED', number_format($user->assigned)); ?></span>
			</p>
		</div>
		<div class="entry-content">
			<div id="user-<?php echo $this->escape($user->username); ?>" style="min-width: 200px; height: 100px;">
				<script type="text/javascript">
					if (jQuery()) {
						var $ = jq, chart<?php echo $user->username; ?>;

						$(document).ready(function() {
							var chart<?php echo $user->username; ?> = $.plot($('#user-<?php echo $user->username; ?>'),
								[{
									color: "#656565",
									label: "<?php echo JText::_('COM_SUPPORT_CLOSED'); ?>",
									data: [<?php echo $closeddata; ?>]
								}], {
								series: {
									lines: {
										show: true,
										fill: true
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
									show: false,
								},
								xaxis: { mode: "time", tickLength: 0, tickDecimals: 0,
									tickFormatter: function (val, axis) {
										var d = new Date(val);
										return month_short[d.getUTCMonth()] + ' \'' + d.getFullYear().toString().substring(2);
									}
								},
								yaxis: { min: 0, max: <?php echo $top; ?> }
							});
						});
					}
				</script>
			</div><!-- / #user -->
			<table class="support-stats-overview">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('COM_SUPPORT_CLOSED'); ?></th>
						<th scope="col" class="block"><?php echo JText::_('COM_SUPPORT_STATS_AVERAGE_LIFETIME'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo number_format($user->total); ?></td>
						<td class="block">
							<?php
							$lifetime = SupportUtilities::calculateAverageLife($user->tickets);
							?>
							<?php echo (isset($lifetime[0])) ? $lifetime[0] : 0; ?> <span><?php echo JText::_('COM_SUPPORT_STATS_DAYS'); ?></span>
							<?php echo (isset($lifetime[1])) ? $lifetime[1] : 0; ?> <span><?php echo JText::_('COM_SUPPORT_STATS_HOURS'); ?></span>
							<?php echo (isset($lifetime[2])) ? $lifetime[2] : 0; ?> <span><?php echo JText::_('COM_SUPPORT_STATS_MINUTES'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- / .entry-content -->
	</div><!-- / .container -->
	<?php
				$j++;
				$z++;
			}
		}
	?>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
	<?php
	}
	?>

	</section><!-- / .main section -->
</form>
