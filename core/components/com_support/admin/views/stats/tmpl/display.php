<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_STATS'), 'support.png');

Toolbar::spacer();
Toolbar::help('stats');

Html::behavior('framework');
Html::behavior('chart');
Html::behavior('chart', 'pie');
Html::behavior('chart', 'resize');

$this->css('stats');

$database = App::get('db');
$sql = "SELECT status
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
		$sql .= " ORDER BY status ASC";
$database->setQuery($sql);
$resolutions = $database->loadObjectList();

$total = count($resolutions);
$res = array();
foreach ($resolutions as $resolution)
{
	if (!isset($res[$resolution->status]))
	{
		$res[$resolution->status] = 1;
	}
	else
	{
		$res[$resolution->status]++;
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
		case 1: $monthname = Lang::txt('January');   break;
		case 2: $monthname = Lang::txt('February');  break;
		case 3: $monthname = Lang::txt('March');     break;
		case 4: $monthname = Lang::txt('April');     break;
		case 5: $monthname = Lang::txt('May');       break;
		case 6: $monthname = Lang::txt('June');      break;
		case 7: $monthname = Lang::txt('July');      break;
		case 8: $monthname = Lang::txt('August');    break;
		case 9: $monthname = Lang::txt('September'); break;
		case 0: $monthname = Lang::txt('October');   break;
		case 11: $monthname = Lang::txt('November');  break;
		case 12: $monthname = Lang::txt('December');  break;
	}
	return $monthname;
}

$base = str_replace('/administrator', '', Request::base(true));
$base = rtrim($base, '/');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="get" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div id="ticket-stats">
		<fieldset id="filter-bar" class="support-stats-filter">
			<label for="ticket-group">
				<?php echo Lang::txt('COM_SUPPORT_STATS_FOR_GROUP'); ?>
			</label>
			<select name="group" id="ticket-group">
				<option value=""<?php if (!$this->group) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
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
			<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo $base; ?>/core/assets/js/excanvas/excanvas.min.js"></script><![endif]-->
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
								margin: [5, 5]
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
			<div class="breakdown">

				<table class="support-stats-overview">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_OPENED_ALL'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_CLOSED_ALL'); ?></th>
							<th scope="col" class="block"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_AVERAGE'); ?></th>
							<th scope="col" class="major"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_UNASSIGNED'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $this->opened['open']; ?></td>
							<td><?php echo $this->opened['closed']; ?></td>
							<td class="block">
								<?php
								$lifetime = \Components\Support\Helpers\Utilities::calculateAverageLife($this->closedTickets);
								?>
								<?php echo (isset($lifetime[0])) ? $lifetime[0] : 0; ?> <span><?php echo Lang::txt('COM_SUPPORT_STATS_DAYS'); ?></span>
								<?php echo (isset($lifetime[1])) ? $lifetime[1] : 0; ?> <span><?php echo Lang::txt('COM_SUPPORT_STATS_HOURS'); ?></span>
								<?php echo (isset($lifetime[2])) ? $lifetime[2] : 0; ?> <span><?php echo Lang::txt('COM_SUPPORT_STATS_MINUTES'); ?></span>
							</td>
							<td class="major"><?php echo $this->opened['unassigned']; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</fieldset>

		<div class="grid">
		<div class="col span6">
			<fieldset class="adminform breakdown pies">
				<legend><span><?php echo Lang::txt('COM_SUPPORT_STATS_FIELDSET_BY_SEVERITY'); ?></span></legend>
				<div id="severities-container" style="min-width: 300px; height: 300px; margin: 60px 0 20px 0;">
					<table class="support-stats-resolutions">
						<thead>
							<tr>
								<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_SEVERITY'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_NUMBER'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_PERCENT'); ?></th>
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

								$severities = \Components\Support\Helpers\Utilities::getSeverities($this->config->get('severities'));

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

		<div class="col span6">
			<fieldset class="adminform breakdown pies">
				<legend><span><?php echo Lang::txt('COM_SUPPORT_STATS_FIELDSET_BY_RESOLUTION'); ?></span></legend>
				<div id="resolutions-container" style="min-width: 300px; height: 300px; margin: 60px 0 20px 0;">
					<table class="support-stats-resolutions">
						<thead>
							<tr>
								<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_RESOLUTION'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_NUMBER'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_PERCENT'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr class="odd">
								<th scope="row"><?php echo Lang::txt('COM_SUPPORT_STATS_NO_RESOLUTION'); ?></th>
								<td><?php echo (isset($res[0])) ? $res[0] : '0'; ?></td>
								<td><?php echo (isset($res[0])) ? $res[0]/$total : '0'; ?></td>
							</tr>
						<?php
							$sr = new \Components\Support\Tables\Status($database);
							$resolutions = $sr->find('list', array('open' => 0));

							$cls = 'odd';
							$data = array(
								"{label: '" . Lang::txt('COM_SUPPORT_STATS_NO_RESOLUTION') . "', data: " . (isset($res[0]) ? $res[0]/$total : '0') . ", color: '" . $colors[0] . "'}"
							);
							$i = 1;
							foreach ($resolutions as $resolution)
							{
								$r  = "{label: '" . $this->escape(addslashes($resolution->title)) . "', data: ";
								$r .= (isset($res[$resolution->id])) ? round(($res[$resolution->id]/$total)*100, 2) : 0;
								$r .= ", color: '" . $colors[$i] . "'}";
								$data[] = $r;

								$cls = ($cls == 'even') ? 'odd' : 'even';
						?>
							<tr class="<?php echo $cls; ?>">
								<th scope="row"><?php echo $this->escape(stripslashes($resolution->title)); ?></th>
								<td><?php echo (isset($res[$resolution->id])) ? $res[$resolution->id] : '0'; ?></td>
								<td><?php echo (isset($res[$resolution->id])) ? round($res[$resolution->id]/$total*100, 2) : '0'; ?></td>
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
		</div>

		<?php
		if ($this->users)
		{
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
						</div><!-- / .col width-50 fltlft -->
						<div class="col span6">
						<?php
					}
					else if ($z == 2)
					{
						$z = 0;
						?>
						</div><!-- / .col width-50 fltrt -->
						</div>
						<div class="col span6">
						<?php
					}
					else
					{
						?>
						<div class="grid">
						<div class="col span6">
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
					$profile = User::getInstance($user->id);
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
					<img src="<?php echo $profile->picture($anon); ?>" alt="<?php echo Lang::txt('COM_SUPPORT_STATS_PHOTO_FOR', $this->escape(stripslashes($user->name))); ?>" />
				</p>
				<p class="entry-title">
					<?php echo $this->escape(stripslashes($user->name)); ?><br />
					<span><?php echo Lang::txt('COM_SUPPORT_STATS_NUM_ASSIGNED', number_format($user->assigned)); ?></span>
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
				<table class="support-stats-overview">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_CLOSED'); ?></th>
							<th scope="col" class="block"><?php echo Lang::txt('COM_SUPPORT_STATS_COL_AVERAGE'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo number_format($user->total); ?></td>
							<td class="block">
								<?php
								$lifetime = \Components\Support\Helpers\Utilities::calculateAverageLife($user->tickets);
								?>
								<?php echo (isset($lifetime[0])) ? $lifetime[0] : 0; ?> <span><?php echo Lang::txt('COM_SUPPORT_STATS_DAYS'); ?></span>
								<?php echo (isset($lifetime[1])) ? $lifetime[1] : 0; ?> <span><?php echo Lang::txt('COM_SUPPORT_STATS_HOURS'); ?></span>
								<?php echo (isset($lifetime[2])) ? $lifetime[2] : 0; ?> <span><?php echo Lang::txt('COM_SUPPORT_STATS_MINUTES'); ?></span>
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
			</div><!-- / .col width-50 fltrt -->
			</div>
		<?php
		}
		?>

	</div><!-- / .section -->

	<input type="hidden" name="task" value="display" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>
