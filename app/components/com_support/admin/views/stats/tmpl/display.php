<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_STATS'), 'support');

Toolbar::spacer();
Toolbar::help('stats');

Html::behavior('framework');
Html::behavior('chart');
Html::behavior('chart', 'pie');
Html::behavior('chart', 'resize');

$this->css('stats');
$this->js('stats.js');

$gidNumber = 0;
if ($group = \Hubzero\User\Group::getInstance($this->filters['group']))
{
	$gidNumber = $group->get('gidNumber');
}

$database = App::get('db');
$sql = "SELECT status
		FROM `#__support_tickets`
		WHERE open=0
		AND type=" . $database->quote($this->filters['type']);
		if (!$this->filters['group'] || $this->filters['group'] == '_none_')
		{
			$sql .= " AND group_id=0";
		}
		else if ($this->filters['group'])
		{
			$sql .= " AND `group_id`=" . $database->quote($gidNumber);
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
		FROM `#__support_tickets`
		WHERE type=" . $database->quote($this->filters['type']);
		if (!$this->filters['group'] || $this->filters['group'] == '_none_')
		{
			$sql .= " AND group_id=0";
		}
		else if ($this->filters['group'])
		{
			$sql .= " AND `group_id`=" . $database->quote($gidNumber);
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
				<option value=""<?php if (!$this->filters['group']) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
				<?php
				if ($this->groups)
				{
					foreach ($this->groups as $group)
					{
						?>
						<option value="<?php echo $group->cn; ?>"<?php if ($this->filters['group'] == $group->cn) { echo ' selected="selected"'; } ?>><?php echo ($group->description) ? stripslashes($this->escape($group->description)) : $this->escape($group->cn); ?></option>
						<?php
					}
				}
				?>
			</select>
			<input type="submit" value="Go" />
		</fieldset>

		<fieldset class="adminform">
			<legend>
				<span><?php echo Date::of($this->first . '-01-01')->format('M Y'); ?> - <?php echo Date::of($this->filters['year'] . '-' . $this->filters['month'] . '-01')->format('M Y'); ?></span>
			</legend>

			<div id="container" class="chart stats-tickets-chart" data-datasets="<?php echo $this->option; ?>-data-openedclosed"></div>
			<script type="application/json" id="<?php echo $this->option; ?>-data-openedclosed">
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
							$c[] = '[' . Date::of($year . '-' . Hubzero\Utility\Str::pad(($k - 1), 2) . '-01')->toUnix() . ',' . $v . ']';
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
							$o[] = '[' . Date::of($year . '-' . Hubzero\Utility\Str::pad(($k - 1), 2) . '-01')->toUnix() . ',' . $v . ']';
						}
					}
					$openeddata = implode(',', $o);
				}
				?>
				{
					"datasets": [
						{
							"color": "#AA4643",
							"label": "<?php echo Lang::txt('COM_SUPPORT_STATS_COL_OPENED_ALL'); ?>",
							"data": [<?php echo $openeddata; ?>]
						},
						{
							"color": "#656565",
							"label": "<?php echo Lang::txt('COM_SUPPORT_STATS_COL_CLOSED_ALL'); ?>",
							"data": [<?php echo $closeddata; ?>]
						}
					]
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
				<div id="severities-container" class="stats-pie-chart" data-datasets="<?php echo $this->option; ?>-data-severity">
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
								$r  = '{"label": "' . $this->escape($severity) . '", "data": ';
								$r .= (isset($sev[$severity])) ? round(($sev[$severity]/$total)*100, 2) : 0;
								$r .= ', "color": "' . $colors[$i] . '"}';

								$data[] = $r;

								$cls = ($cls == 'even') ? 'odd' : 'even';
								?>
								<tr class="<?php echo $cls; ?>">
									<th scope="row"><?php echo $this->escape($severity); ?></th>
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
				<script type="application/json" id="<?php echo $this->option; ?>-data-severity">
					{
						"datasets": [<?php echo implode(',' . "\n", $data); ?>]
					}
				</script>
			</fieldset>
		</div>

		<div class="col span6">
			<fieldset class="adminform breakdown pies">
				<legend><span><?php echo Lang::txt('COM_SUPPORT_STATS_FIELDSET_BY_RESOLUTION'); ?></span></legend>
				<div id="resolutions-container" class="stats-pie-chart" data-datasets="<?php echo $this->option; ?>-data-resolution">
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
							$resolutions = Components\Support\Models\Status::all()->whereEquals('open', 0)->rows();

							$cls = 'odd';
							$data = array(
								'{"label": "' . Lang::txt('COM_SUPPORT_STATS_NO_RESOLUTION') . '", "data": ' . (isset($res[0]) ? $res[0]/$total : '0') . ', "color": "' . $colors[0] . '"}'
							);
							$i = 1;
							foreach ($resolutions as $resolution)
							{
								$r  = '{"label": "' . $this->escape($resolution->title) . '", "data": ';
								$r .= (isset($res[$resolution->id])) ? round(($res[$resolution->id]/$total)*100, 2) : 0;
								$r .= ', "color": "' . $colors[$i] . '"}';
								$data[] = $r;

								$cls = ($cls == 'even') ? 'odd' : 'even';
								?>
								<tr class="<?php echo $cls; ?>">
									<th scope="row"><?php echo $this->escape($resolution->title); ?></th>
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
				<script type="application/json" id="<?php echo $this->option; ?>-data-resolution">
					{
						"datasets": [<?php echo implode(',' . "\n", $data); ?>]
					}
				</script>
			</fieldset>
		</div>
		</div>

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
						</div><!-- / .col span6 -->
						<div class="col span6">
						<?php
					}
					else if ($z == 2)
					{
						$z = 0;
						?>
						</div><!-- / .col span6 -->
						</div><!-- / .grid -->
						<div class="grid">
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
								$c[] = '[' . Date::of($year . '-' . Hubzero\Utility\Str::pad(($k - 1), 2) . '-01')->toUnix() . ',' . $v . ']';
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
								<div id="user-<?php echo $this->escape($user->username); ?>" class="stats-user-chart" data-datasets="<?php echo $this->option; ?>-data-user<?php echo $user->id; ?>">
									<script type="application/json" id="<?php echo $this->option; ?>-data-user<?php echo $user->id; ?>">
										{
											"top": <?php echo $top; ?>,
											"datasets": [{
												"color": "#656565",
												"label": "Closed",
												"data": [<?php echo $closeddata; ?>]
											}]
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
			</div><!-- / .col span6 -->
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
