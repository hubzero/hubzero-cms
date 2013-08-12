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

/*$st = new SupportTicket($database);

if (intval($this->config->get('cache', 1)))
{
	$cache =& JFactory::getCache('callback');
	$cache->setCaching(1);
	$cache->setLifeTime(intval($this->params->get('cache_time', 900)));

	$resolutions = $cache->call(array($st, 'findResolutions'), $this->type);
	$severities  = $cache->call(array($st, 'findSeverities'), $this->type);
}
else
{
	$resolutions = $st->find('resolved', array('type' => $this->type, 'open' => 0));
	$severities  = $st->find('severity', array('type' => $this->type));
}*/

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

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<ul id="useroptions">
		<li>
			<a class="icon-browse browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>">
				<?php echo JText::_('Tickets'); ?>
			</a>
		</li>
		<li class="last">
			<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>">
				<?php echo JText::_('SUPPORT_NEW_TICKET'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

	<ul class="sub-menu">
		<li id="sm-1"<?php if ($this->type == 0) { echo ' class="active"'; } ?>><a class="tab" rel="submitted" href="/support/stats"><span><?php echo JText::_('Submitted Tickets'); ?></span></a></li>
		<li id="sm-2"<?php if ($this->type == 1) { echo ' class="active"'; } ?>><a class="tab" rel="automatic" href="/support/stats?type=automatic"><span><?php echo JText::_('Automatic Tickets'); ?></span></a></li>
	</ul>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>" method="get" enctype="multipart/form-data">
	<div class="main section" id="ticket-stats">
		<div class="two columns first">
			<!-- <h3 class="time-range">
				<?php echo getMonthName(1) . ' ' . $this->first ?> - <?php echo getMonthName($this->month) . ' ' . $this->year; ?>
			</h3> -->
			<p class="time-range">
				<label for="start-date">From</label> <input type="text" name="start" id="start-date" value="<?php echo $this->escape($this->start); ?>" size="7" />
				<label for="end-date">to</label> <input type="text" name="end" id="end-date" value="<?php echo $this->escape($this->end); ?>" size="7" />
			</p>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<fieldset class="support-stats-filter">
				<label for="ticket-group">
					<?php echo JText::_('Show for group:'); ?>
				</label>
				<select name="group" id="ticket-group">
					<option value=""<?php if (!$this->group) { echo ' selected="selected"'; } ?>><?php echo JText::_('[ all ]'); ?></option>
					<option value="_none_"<?php if ($this->group == '_none_') { echo ' selected="selected"'; } ?>><?php echo JText::_('[ none ]'); ?></option>
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
		</div><!-- / .two columns second -->
		<div class="clear"></div>

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
							$o[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']'; // - $this->closedmonths[$k];
						}
					}
					$openeddata = implode(',', $o);
				}
			?>
			<script src="/media/system/js/flot/jquery.flot.min.js"></script>
			<script src="/media/system/js/flot/jquery.flot.tooltip.min.js"></script>
			<script src="/media/system/js/flot/jquery.flot.pie.min.js"></script>
			<script src="/media/system/js/flot/jquery.flot.resize.js"></script>
			<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/media/system/js/excanvas/excanvas.min.js"></script><![endif]-->
			<script type="text/javascript">
				if (!jq) {
					var jq = $;
				}
				if (jQuery()) {
					var $ = jq, 
						chart, 
						month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
						datasets = [
							{
								color: "#AA4643", //#93ACCA
								label: "Opened",
								data: [<?php echo $openeddata; ?>]
							},
							{
								color: "#656565", //#CFCFAB
								label: "Closed",
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
									return month_short[d.getUTCMonth()] + ' ' + d.getFullYear();//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
								}
							},
							yaxis: { min: 0 }
						});
						
						/*$('#start-date').datepicker({
							dateFormat: "yy-mm-dd",
							constrainInput: true//,
							//setDate: new Date($("input#id_due_date").attr("value"))
						});
						$('#end-date').datepicker({
							dateFormat: "yy-mm-dd",
							constrainInput: true//,
							//setDate: new Date($("input#id_due_date").attr("value"))
						});*/
					});
				}
			</script>
		</div><!-- / #container -->

		<div class="container breakdown">
			<table class="support-stats-overview" summary="<?php echo JText::_('Overview of open support tickets'); ?>">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('Opened (all time)'); ?></th>
						<th scope="col"><?php echo JText::_('Closed (all time)'); ?></th>
						<th scope="col" class="block"><?php echo JText::_('Average lifetime'); ?></th>
						<th scope="col" class="major"><?php echo JText::_('Unassigned'); ?></th>
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
							<?php echo (isset($lifetime[0])) ? $lifetime[0] : 0; ?> <span><?php echo JText::_('days'); ?></span> 
							<?php echo (isset($lifetime[1])) ? $lifetime[1] : 0; ?> <span><?php echo JText::_('hours'); ?></span> 
							<?php echo (isset($lifetime[2])) ? $lifetime[2] : 0; ?> <span><?php echo JText::_('minutes'); ?></span>
						</td>
						<td class="major"><?php echo $this->opened['unassigned']; ?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- / .container breakdown -->

		<div class="container breakdown pies">
			<div class="two columns first">
				<h3><?php echo JText::_('Tickets by severity'); ?></h3>
				<div id="severities-container" style="min-width: 270px; height: 270px;">
					<table class="support-stats-resolutions" summary="<?php echo JText::_('Breakdown of number of tickets for each severity'); ?>">
						<thead>
							<tr>
								<th scope="col"><?php echo JText::_('Severity'); ?></th>
								<th scope="col"><?php echo JText::_('Number'); ?></th>
								<th scope="col"><?php echo JText::_('Percent'); ?></th>
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
							/*legend: { 
								show: false
							},*/
							series: {
								pie: { 
									/*innerRadius: 0.5,*/
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
			</div><!-- / .two columns first -->
			<div class="two columns second">
				<h3><?php echo JText::_('Tickets by resolution'); ?></h3>
				<div id="resolutions-container" style="min-width: 270px; height: 270px;">
					<table class="support-stats-resolutions" summary="<?php echo JText::_('Breakdown of people and the number of tickets closed'); ?>">
						<thead>
							<tr>
								<th scope="col"><?php echo JText::_('Resolution'); ?></th>
								<th scope="col"><?php echo JText::_('Number'); ?></th>
								<th scope="col"><?php echo JText::_('Percent'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr class="odd">
								<th scope="row"><?php echo JText::_('No resolution'); ?></th>
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
							$nores = "{label: '" . JText::_('No resolution') . "', data: " . (isset($res['noresolution']) ? $res['noresolution']/$total : '0') . ", color: '" . $colors[$i] . "'}";
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
							/*legend: { 
								show: false
							},*/
							series: {
								pie: { 
									/*innerRadius: 0.5,*/
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
			</div><!-- / .two columns second -->
			<div class="clear"></div>
		</div><!-- / .container -->

	<?php
	if ($this->users)
	{
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_User_Profile_Helper');
		//$chunked = array_chunk($this->users, ceil(count($this->users) / 2));
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
					</div><!-- / .two columns first -->
					<div class="two columns second">
					<?php
				}
				else if ($z == 2)
				{
					$z = 0;
					?>
					</div><!-- / .two columns second -->
					<div class="clear"></div>
					<div class="two columns first">
					<?php
				}
				else
				{
					?>
					<div class="two columns first">
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
				$profile = Hubzero_User_Profile::getInstance($user->id);
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
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($profile, $anon); ?>" alt="<?php echo JText::sprintf('Photo for %s', $this->escape(stripslashes($user->name))); ?>" />
			</p>
			<p class="entry-title">
				<?php echo $this->escape(stripslashes($user->name)); ?><br />
				<span><?php echo JText::sprintf('%s assigned', number_format($user->assigned)); ?></span>
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
									color: "#656565", //#93ACCA
									label: "Closed",
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
									show: false,
								},
								xaxis: { mode: "time", tickLength: 0, tickDecimals: 0,
									tickFormatter: function (val, axis) {
										var d = new Date(val);
										return month_short[d.getUTCMonth()] + ' \'' + d.getFullYear().toString().substring(2);//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
									}
								},
								yaxis: { min: 0, max: <?php echo $top; ?> }
							});
						});
					}
				</script>
			</div><!-- / #user -->
			<table class="support-stats-overview" summary="<?php echo JText::_('Overview of open support tickets'); ?>">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('Closed'); ?></th>
						<th scope="col" class="block"><?php echo JText::_('Average lifetime'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo number_format($user->total); ?></td>
						<td class="block">
							<?php
							$lifetime = SupportUtilities::calculateAverageLife($user->tickets);
							?>
							<?php echo (isset($lifetime[0])) ? $lifetime[0] : 0; ?> <span><?php echo JText::_('days'); ?></span> 
							<?php echo (isset($lifetime[1])) ? $lifetime[1] : 0; ?> <span><?php echo JText::_('hours'); ?></span> 
							<?php echo (isset($lifetime[2])) ? $lifetime[2] : 0; ?> <span><?php echo JText::_('minutes'); ?></span>
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
		</div><!-- / .two columns second -->
		<div class="clear"></div>
	<?php
	}
	?>

	</div><!-- / .main section -->
</form>
