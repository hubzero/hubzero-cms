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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Ticket Stats' ).' ]</small></small>', 'support.png' );

JToolBarHelper::spacer();
JToolBarHelper::help('stats.html', true);

$database = JFactory::getDBO();
$sql = "SELECT resolved
		FROM #__support_tickets
		WHERE open=0 
		AND type='{$this->type}' ";
		if ($this->group)
		{
			$sql .= " AND `group`='{$this->group}' ";
		}
		else
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
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
		if ($this->group)
		{
			$sql .= " AND `group`='{$this->group}' ";
		}
		else
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
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
<form action="index.php" method="get" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div id="ticket-stats">
		<fieldset id="filter-bar" class="support-stats-filter">
			<label for="ticket-group">
				<?php echo JText::_('Show for group:'); ?>
			</label>
			<select name="group" id="ticket-group">
				<option value=""<?php if (!$this->group) { echo ' selected="selected"'; } ?>><?php echo JText::_('[ none ]'); ?></option>
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

		<fieldset class="adminform">
			<legend>
				<span><?php echo getMonthName(1) . ' ' . $this->first ?> - <?php echo getMonthName($this->month) . ' ' . $this->year; ?></span>
			</legend>
			
			<div id="container" style="min-width: 400px; height: 200px; margin: 60px 20px 20px 20px;"></div>
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
			<script src="/media/system/js/jquery.js"></script>
			<script src="/media/system/js/jquery.noconflict.js"></script>
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
									return month_short[d.getUTCMonth()];//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
								}
							},
							yaxis: { min: 0 }
						});
					});
				}
			</script>
			<div class="clr"></div>
		</fieldset>

		<fieldset class="adminform breakdown">
			<!-- <legend>
				<span><?php echo JText::_('Breakdown'); ?></span>
			</legend> -->
			<div class="breakdown">
	
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
			</div>
		</fieldset>
		
		<div class="col width-50 fltlft">
			<fieldset class="adminform breakdown pies">
				<legend><span><?php echo JText::_('Tickets by severity'); ?></span></legend>
				<div id="severities-container" style="min-width: 300px; height: 300px; margin: 60px 0 20px 0;">
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
									'#7c7c7c',
									'#515151',
									'#404040',//'#d9d9d9',
									'#3d3d3d',
									'#797979',
									'#595959',
									'#e5e5e5',
									'#828282',
									'#404040',
									'#6a6a6a',
									'#bcbcbc',
									'#515151',
									'#d9d9d9',
									'#3d3d3d',
									'#797979',
									'#595959',
									'#e5e5e5',
									'#828282',
									'#404040',
									'#3a3a3a'
								);

								$severities = SupportUtilities::getSeverities($this->config->get('severities'));

								$cls = 'odd';
								$data = array();
								$i = 0;
								foreach ($severities as $severity)
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
					$(document).ready(function() {
						severityPie = $.plot($("#severities-container"), [<?php echo implode(',' . "\n", $data); ?>], {
							legend: { 
								show: false
							},
							series: {
								pie: { 
									/*innerRadius: 0.5,*/
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
			</fieldset>
		</div>
		
		<div class="col width-50 fltrt">
			<fieldset class="adminform breakdown pies">
				<legend><span><?php echo JText::_('Tickets by resolution'); ?></span></legend>
				<div id="resolutions-container" style="min-width: 300px; height: 300px; margin: 60px 0 20px 0;">
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
							$data = array(
								"{label: '" . JText::_('No resolution') . "', data: " . (isset($res['noresolution']) ? $res['noresolution']/$total : '0') . ", color: '" . $colors[0] . "'}"
							);
							$i = 1;
							foreach ($resolutions as $resolution)
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
						?>
						</tbody>
					</table>
				</div><!-- / #resolutions-container -->
				<script type="text/javascript">
				if (jQuery()) {
					var $ = jq, resolutionPie;
					$(document).ready(function() {
						resolutionPie = $.plot($("#resolutions-container"), [<?php echo implode(',' . "\n", $data); ?>], {
							legend: { 
								show: false
							},
							series: {
								pie: { 
									/*innerRadius: 0.5,*/
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
			</fieldset>
		</div>
		<div class="clr"></div>

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
						<div class="col width-50 fltrt">
						<?php
					}
					else if ($z == 2)
					{
						$z = 0;
						?>
						</div><!-- / .two columns second -->
						<div class="clr"></div>
						<div class="col width-50 fltlft">
						<?php
					}
					else
					{
						?>
						<div class="col width-50 fltlft">
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
		<fieldset class="adminform">
			<div class="breakdown">
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
											return month_short[d.getUTCMonth()];//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
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
			</div>
		</fieldset><!-- / .container -->
		<?php
					$j++;
					$z++;
				}
			}
		?>
			</div><!-- / .two columns second -->
			<div class="clr"></div>
		<?php
		}
		?>

	</div><!-- / .section -->

	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
