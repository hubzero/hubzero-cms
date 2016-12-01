<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//Html::behavior('chart');
//Html::behavior('chart', 'resize');

$this->css();
$this->js('flot/jquery.colorhelpers.min.js', 'system')
     ->js('jquery.flot.min.js')
     ->js('jquery.flot.time.min.js')
     ->js('jquery.flot.resize.min.js')
     ->js('jquery.flot.canvas.min.js')
     ->js('flot/jquery.flot.tooltip.min.js', 'system')
     ->js('jquery.flot.selection.min.js')
     ->js('base64.js')
     ->js('canvas2image.js')
     ->js('jquery.flot.saveAsImage.js');

$db = Components\Usage\Helpers\Helper::getUDBO();

$datetime = $this->datetime;
$period   = $this->period;

if ($this->message) { ?>
	<p class="info"><?php echo nl2br($this->message); ?></p>
<?php } ?>

<nav class="time-periods">
	<ul>
		<li<?php if ($this->period == 12) { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option=com_usage&task=' . $this->element . '&period=prior12'); ?>"><span><?php echo Lang::txt('PLG_USAGE_PERIOD_PRIOR12'); ?></span></a></li>
		<li<?php if ($this->period == 1) {  echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option=com_usage&task=' . $this->element . '&period=month'); ?>"><span><?php echo Lang::txt('PLG_USAGE_PERIOD_MONTH'); ?></span></a></li>
		<li<?php if ($this->period == 3) {  echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option=com_usage&task=' . $this->element . '&period=qtr'); ?>"><span><?php echo Lang::txt('PLG_USAGE_PERIOD_QTR'); ?></span></a></li>
		<li<?php if ($this->period == 0) {  echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option=com_usage&task=' . $this->element . '&period=year'); ?>"><span><?php echo Lang::txt('PLG_USAGE_PERIOD_YEAR'); ?></span></a></li>
		<li<?php if ($this->period == 13) { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option=com_usage&task=' . $this->element . '&period=fiscal'); ?>"><span><?php echo Lang::txt('PLG_USAGE_PERIOD_FISCAL'); ?></span></a></li>
	</ul>
</nav>

<section class="usage-summary usage-users">
	<h3><?php echo Lang::txt('PLG_USAGE_OVERVIEW_USERS'); ?></h3>

	<?php
	$id = 1; // Users
	$oldest = null;
	$highest = 0;

	$currentVisit = new stdClass;
	$currentVisit->value = 0;
	$currentVisit->datetime = $datetime;

	$sql = "SELECT value, valfmt, datetime
			FROM `summary_user_vals`
			WHERE rowid=" . $db->quote($id) . " AND period=" . $db->quote($period) . " AND datetime<=" . $db->quote($datetime) . " AND colid=" . $db->quote($id) . "
			ORDER BY datetime ASC";
	$db->setQuery($sql);
	$results = $db->loadObjectList();

	$visits = array();

	if ($results)
	{
		//$visits[] = "[new Date('2000/01/01'),0]";

		foreach ($results as $result)
		{
			$highest = $result->value > $highest ? $result->value : $highest;
			$visits[] = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->value . "]";
		}

		$currentVisit = end($results);
	}

	$id = 4; // Download Users

	$startSelection = $datetime;
	$currentDownload = new stdClass;
	$currentDownload->value = 0;
	$currentDownload->datetime = $datetime;

	$sql = "SELECT value, valfmt, datetime
			FROM `summary_user_vals`
			WHERE rowid=" . $db->quote($id) . " AND period=" . $db->quote($period) . " AND datetime<=" . $db->quote($datetime) . " AND colid=" . $db->quote(1) . "
			ORDER BY datetime ASC";
	$db->setQuery($sql);
	$results = $db->loadObjectList();

	$downloads = array();

	if ($results)
	{
		foreach ($results as $result)
		{
			$highest = $result->value > $highest ? $result->value : $highest;
			$downloads[] = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->value . "]";
		}

		$currentDownload = end($results);

		$startSelection = $results[0]->datetime;
		if (count($results) > 12)
		{
			$reversed = array_reverse($results);
			$startSelection = $reversed[11]->datetime;
		}
	}

	$from = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $startSelection));
	$to   = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $currentDownload->datetime));
	?>
	<div class="chart-wrap">
		<div id="chart-users" class="chart line multi-line"></div>
		<div id="chart-users-overview" class="chart line multi-line overview"></div>
	</div>

	<div class="grid charts">
		<div class="col span6 usage-stat">
			<!-- Selected point value. Defaults to most current date. -->
			<span class="usage-value" id="users-visits"><?php echo number_format($currentVisit->value); ?></span>
			<span class="usage-label"><?php echo Lang::txt('PLG_USAGE_OVERVIEW_VISITS'); ?></span>

			<div class="grid">
				<?php
				$residence    = array();
				$organization = array();

				$sql = "SELECT value, valfmt FROM summary_user_vals WHERE rowid=" . $db->quote(1) . " AND period=" . $db->quote($period) . " AND datetime=" . $db->quote($currentVisit->datetime) . " ORDER BY colid";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					$res_iden = 1;
					$org_iden = 1;

					$i = 0;

					$highest = 1;

					foreach ($results as $row)
					{
						$i++;

						if ($i == 2)
						{
							$highest = $row->value;
						}

						if ($i == 7)
						{
							$highest = $row->value;
						}

						if ($row->valfmt == 2 && $highest > 100)
						{
							$row->value = number_format(($row->value / $highest) * 100);
						}

						switch ($i)
						{
							case 3:
								$residence[] = array(
									'column' => 'US',
									'value'  => $row->value,
									'color'  => 'rgba(19, 164, 164, 0.7)'
								);
							break;
							case 4:
								$residence[] = array(
									'column' => 'Asia',
									'value'  => $row->value,
									'color'  => 'rgba(222, 176, 88, 0.7)'
								);
							break;
							case 5:
								$residence[] = array(
									'column' => 'Europe',
									'value'  => $row->value,
									'color'  => 'rgba(233, 95, 87, 0.7)'
								);
							break;
							case 6:
								$residence[] = array(
									'column' => 'Other',
									'value'  => $row->value,
									'color'  => 'rgba(124, 145, 64, 0.7)'
								);
							break;

							case 8:
								$organization[] = array(
									'column' => 'Education',
									'value'  => $row->value,
									'color'  => 'rgba(19, 164, 164, 0.7)'
								);
							break;
							case 9:
								$organization[] = array(
									'column' => 'Industry',
									'value'  => $row->value,
									'color'  => 'rgba(222, 176, 88, 0.7)'
								);
							break;
							case 10:
								$organization[] = array(
									'column' => 'Government',
									'value'  => $row->value,
									'color'  => 'rgba(233, 95, 87, 0.7)'
								);
							break;
							case 11:
								$organization[] = array(
									'column' => 'Other',
									'value'  => $row->value,
									'color'  => 'rgba(124, 145, 64, 0.7)'
								);
							break;
							break;

							default:
								//$val = $row->value;
							break;
						}
					}
				}
				?>
				<div class="col span6">
					<div class="bar-table">
						<h4><?php echo Lang::txt('PLG_USAGE_OVERVIEW_ID_BY_RES'); ?></h4>
						<ul class="bars">
							<?php foreach ($residence as $res) { ?>
								<li id="users-visits-res-<?php echo strtolower($res['column']); ?>">
									<span class="item-label"><?php echo $res['column']; ?></span>
									<span class="item-value"><?php echo number_format($res['value']); ?>%</span>
									<span class="bar-container">
										<span class="bar-value" style="background-color: <?php echo $res['color']; ?>; width: <?php echo number_format($res['value']); ?>%;"></span>
									</span>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div><!-- / .col -->
				<div class="col span6 omega">
					<div class="bar-table">
						<h4><?php echo Lang::txt('PLG_USAGE_OVERVIEW_ID_BY_ORG'); ?></h4>
						<ul class="bars">
							<?php foreach ($organization as $res) { ?>
								<li id="users-visits-org-<?php echo strtolower($res['column']); ?>">
									<span class="item-label"><?php echo $res['column']; ?></span>
									<span class="item-value"><?php echo number_format($res['value']); ?>%</span>
									<span class="bar-container">
										<span class="bar-value" style="background-color: <?php echo $res['color']; ?>; width: <?php echo number_format($res['value']); ?>%;"></span>
									</span>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div><!-- / .col -->
			</div><!-- / .grid -->
		</div>
		<div class="col span6 omega usage-stat">
			<!-- Selected point value. Defaults to most current date. -->
			<span class="usage-value" id="users-downloads"><?php echo number_format($currentDownload->value); ?></span>
			<span class="usage-label"><?php echo Lang::txt('PLG_USAGE_OVERVIEW_DOWNLOADS'); ?></span>

			<div class="grid">
				<?php
				$residence = array();
				$organization = array();

				$sql = "SELECT value, valfmt FROM summary_user_vals WHERE rowid=" . $db->quote(4) . " AND period=" . $db->quote($period) . " AND datetime=" . $db->quote($currentDownload->datetime) . " ORDER BY colid";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					$res_iden = 1;
					$org_iden = 1;

					$i = 0;

					$highest = 1;

					foreach ($results as $row)
					{
						$i++;

						if ($i == 2)
						{
							$highest = $row->value;
						}

						if ($i == 7)
						{
							$highest = $row->value;
						}

						if ($row->valfmt == 2 && $highest > 100)
						{
							$row->value = number_format(($row->value / $highest) * 100);
						}

						switch ($i)
						{
							case 3:
								$residence[] = array(
									'column' => 'US',
									'value'  => $row->value,
									'color'  => 'rgba(19, 164, 164, 0.7)'
								);
							break;
							case 4:
								$residence[] = array(
									'column' => 'Asia',
									'value'  => $row->value,
									'color'  => 'rgba(222, 176, 88, 0.7)'
								);
							break;
							case 5:
								$residence[] = array(
									'column' => 'Europe',
									'value'  => $row->value,
									'color'  => 'rgba(233, 95, 87, 0.7)'
								);
							break;
							case 6:
								$residence[] = array(
									'column' => 'Other',
									'value'  => $row->value,
									'color'  => 'rgba(124, 145, 64, 0.7)'
								);
							break;

							case 8:
								$organization[] = array(
									'column' => 'Education',
									'value'  => $row->value,
									'color'  => 'rgba(19, 164, 164, 0.7)'
								);
							break;
							case 9:
								$organization[] = array(
									'column' => 'Industry',
									'value'  => $row->value,
									'color'  => 'rgba(222, 176, 88, 0.7)'
								);
							break;
							case 10:
								$organization[] = array(
									'column' => 'Government',
									'value'  => $row->value,
									'color'  => 'rgba(233, 95, 87, 0.7)'
								);
							break;
							case 11:
								$organization[] = array(
									'column' => 'Other',
									'value'  => $row->value,
									'color'  => 'rgba(124, 145, 64, 0.7)'
								);
							break;
							break;

							default:
								//$val = $row->value;
							break;
						}
					}
				}
				?>
				<div class="col span6">
					<div class="bar-table">
						<h4><?php echo Lang::txt('PLG_USAGE_OVERVIEW_ID_BY_RES'); ?></h4>
						<ul class="bars">
							<?php foreach ($residence as $res) { ?>
								<li id="users-downloads-res-<?php echo strtolower($res['column']); ?>">
									<span class="item-label"><?php echo $res['column']; ?></span>
									<span class="item-value"><?php echo number_format($res['value']); ?>%</span>
									<span class="bar-container">
										<span class="bar-value" style="background-color: <?php echo $res['color']; ?>; width: <?php echo number_format($res['value']); ?>%;"></span>
									</span>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div><!-- / .col -->
				<div class="col span6 omega">
					<div class="bar-table">
						<h4><?php echo Lang::txt('PLG_USAGE_OVERVIEW_ID_BY_ORG'); ?></h4>
						<ul class="bars">
							<?php foreach ($organization as $res) { ?>
								<li id="users-downloads-org-<?php echo strtolower($res['column']); ?>">
									<span class="item-label"><?php echo $res['column']; ?></span>
									<span class="item-value"><?php echo number_format($res['value']); ?>%</span>
									<span class="bar-container">
										<span class="bar-value" style="background-color: <?php echo $res['color']; ?>; width: <?php echo number_format($res['value']); ?>%;"></span>
									</span>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div><!-- / .col -->
			</div><!-- / .grid -->
		</div>
	</div>
</section>

<section class="usage-summary usage-simulation">
	<h3><?php echo Lang::txt('PLG_USAGE_OVERVIEW_SIMULATION'); ?></h3>

	<div class="grid charts">
		<div class="col span6 usage-stat">
			<?php
			$currentSimuser = new stdClass;
			$currentSimuser->value = 0;
			$currentSimuser->datetime = $datetime;

			$simusers = array();

			$db->setQuery(
				"SELECT *
				FROM `summary_simusage_vals`
				WHERE `period` = " . $db->quote($period) . "
				AND `rowid`= " . $db->quote(1) . "
				ORDER BY `datetime` ASC"
			);
			$results = $db->loadObjectList();
			if ($results)
			{
				foreach ($results as $result)
				{
					$simusers[] = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->value . "]";
				}

				$currentSimuser = end($results);
			}
			?>
			<span class="usage-value" id="simulation-users"><?php echo number_format($currentSimuser->value); ?></span>
			<span class="usage-label"><?php echo Lang::txt('PLG_USAGE_OVERVIEW_SIMULATION_USERS'); ?></span>

			<div class="chart-wrap">
				<div id="chart-sim-users" class="chart line"></div>
			</div>
		</div>
		<div class="col span6 omega usage-stat">
			<?php
			$currentSimjob = new stdClass;
			$currentSimjob->value = 0;
			$currentSimjob->datetime = $datetime;

			$simjobs = array();

			$db->setQuery(
				"SELECT *
				FROM `summary_simusage_vals`
				WHERE `period` = " . $db->quote($period) . "
				AND `rowid`= " . $db->quote(2) . "
				ORDER BY `datetime` ASC"
			);
			$results = $db->loadObjectList();
			if ($results)
			{
				foreach ($results as $result)
				{
					$simjobs[] = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->value . "]";
				}

				$currentSimjob = end($results);
			}
			?>
			<span class="usage-value" id="simulation-jobs"><?php echo number_format($currentSimjob->value); ?></span>
			<span class="usage-label"><?php echo Lang::txt('PLG_USAGE_OVERVIEW_SIMULATION_JOBS'); ?></span>

			<div class="chart-wrap">
				<div id="chart-sim-jobs" class="chart line"></div>
			</div>
		</div>
	</div>
	<div class="usage-stats">
		<?php
		$datetime = ($currentSimjob->datetime >= $currentSimuser->datetime ? $currentSimjob->datetime : $currentSimuser->datetime);

		$db->setQuery(
			"SELECT a.label,b.value,b.valfmt,a.plot,a.id
			FROM `summary_simusage` AS a
			INNER JOIN `summary_simusage_vals` AS b
			WHERE a.id=b.rowid AND b.period = " . $db->quote($period) . " AND b.datetime = " . $db->quote($datetime) . " AND a.id > 2
			ORDER BY a.id"
		);
		$results = $db->loadObjectList();
		if ($results)
		{
			?>
			<div class="grid">
				<?php foreach ($results as $i => $result) { ?>
					<div class="col span3 usage-stat<?php if ($i == 3 || $i == 7) { echo ' omega'; } ?>">
						<span class="usage-value" id="simulation-<?php echo preg_replace('/[^a-z0-9\-_]/', '', strtolower($result->label)); ?>"><?php echo plgUsageOverview::formatValue($result->value, $result->valfmt); ?></span>
						<span class="usage-label"><?php echo $result->label; ?></span>
					</div>
					<?php if ($i == 3 || $i == 7) { echo '</div><div class="grid">'; } ?>
				<?php } ?>
			</div>
			<?php
		}
		?>
	</div>
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
						color: "#5BA0BD", //rgba(133, 95, 123, 0.5)", //#CFCFAB
						label: "<?php echo Lang::txt('PLG_USAGE_OVERVIEW_VISITS'); ?>",
						data: [<?php echo implode(',', $visits); ?>]
					},
					{
						color: "rgba(133, 95, 123, 0.5)",
						label: "<?php echo Lang::txt('PLG_USAGE_OVERVIEW_DOWNLOADS'); ?>",
						data: [<?php echo implode(',', $downloads); ?>]
					}
				];

			$(document).ready(function() {
				var cusers = $('#chart-users');
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

					if (chart_users) {
						// Unhighlight any previously clicked points
						chart_users.unhighlight();
						// Highlight the current point
						chart_users.highlight(0, item.dataIndex);
						chart_users.highlight(1, item.dataIndex);
					}
					if (chart_sim_users) {
						chart_sim_users.unhighlight();
						chart_sim_users.highlight(0, item.dataIndex);
					}
					if (chart_sim_jobs) {
						chart_sim_jobs.unhighlight();
						chart_sim_jobs.highlight(0, item.dataIndex);
					}

					$.getJSON("<?php echo Route::url('index.php?option=com_usage&task=' . $this->element . '&action=getUsageForDate&period=' . $period . '&datetime=', false); ?>" + yyyy + "-" + mm, function(data){
						if (data) {
							var tmp = null;

							$('#users-visits').text(data.visits.total); //datasets[0].data[item.dataIndex][1]);
							$('#users-downloads').text(data.downloads.total); //datasets[1].data[item.dataIndex][1]);
							$('#simulation-users').text(data.simulation_users); //datasets[1].data[item.dataIndex][1]);
							$('#simulation-jobs').text(data.simulation_jobs); //datasets[1].data[item.dataIndex][1]);

							for (var i = 0; i < data.visits.residence.length; i++)
							{
								tmp = $('#' + data.visits.residence[i].key);
								if (tmp.length) {
									tmp.find('.item-value').text(data.visits.residence[i].value + '%');
									tmp.find('.bar-value').width(data.visits.residence[i].value + '%');
								}
							}

							for (var i = 0; i < data.visits.organization.length; i++)
							{
								tmp = $('#' + data.visits.organization[i].key);
								if (tmp.length) {
									tmp.find('.item-value').text(data.visits.organization[i].value + '%');
									tmp.find('.bar-value').width(data.visits.organization[i].value + '%');
								}
							}

							for (var i = 0; i < data.downloads.residence.length; i++)
							{
								tmp = $('#' + data.downloads.residence[i].key);
								if (tmp.length) {
									tmp.find('.item-value').text(data.downloads.residence[i].value + '%');
									tmp.find('.bar-value').width(data.downloads.residence[i].value + '%');
								}
							}

							for (var i = 0; i < data.downloads.organization.length; i++)
							{
								tmp = $('#' + data.downloads.organization[i].key);
								if (tmp.length) {
									tmp.find('.item-value').text(data.downloads.organization[i].value + '%');
									tmp.find('.bar-value').width(data.downloads.organization[i].value + '%');
								}
							}

							for (var i = 0; i < data.simulation.length; i++)
							{
								tmp = $('#' + data.simulation[i].key);
								if (tmp.length) {
									tmp.text(data.simulation[i].value);
								}
							}
						}
					});
				};

				var options = {
					series: {
						lines: {
							show: true,
							fill: false
						},
						points: {
							show: false
						},
						shadowSize: 0
					},
					grid: {
						color: 'rgba(0, 0, 0, 0.6)',
						borderWidth: 1,
						borderColor: 'rgba(0, 0, 0, 0.2)', //'transparent',
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
						position: "se",
						backgroundColor: 'transparent',
						margin: [0, -30]
					},
					xaxis: {
						mode: "time",
						//tickLength: 0,
						position: 'top',
						tickDecimals: 0,
						min: new Date('<?php echo $from; ?>'),
						max: new Date('<?php echo $to; ?>'),
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							//return month_short[d.getUTCMonth()] + ' ' + d.getFullYear();//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
							return (d.getUTCMonth() + 1) + "/" + d.getUTCFullYear().toString().substr(2,2);
						}//,
						//zoomRange: [0.1, 1],
						//panRange: [new Date('2000/01/01'), new Date('2014/12/01')]
					},
					yaxis: {
						min: 0,
						tickFormatter: function (val, axis) {
							if (val > 1000) {
								val = (val / 1000) + ' K';
							}
							return val;
						}
					},
					zoom: {
						//interactive: true
					},
					pan: {
						interactive: true
					}
				};

				cusers.bind("plotclick", function(event, pos, item) {
					return updateCharts(event, pos, item);
				});
				cusers.bind("plotselected", function (event, ranges) {
					if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
						ranges.xaxis.to = ranges.xaxis.from + 0.00001;
					}
					if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
						ranges.yaxis.to = ranges.yaxis.from + 0.00001;
					}
					chart_users = $.plot(cusers, datasets,
						$.extend(true, {}, options, {
							xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
						})
					);

					// don't fire event on the overview to prevent eternal loop
					plotUO.setSelection(ranges, true);
				});
				var chart_users = $.plot(cusers, datasets, options);
				chart_users.highlight(0, <?php echo (count($visits) - 1); ?>);
				chart_users.highlight(1, <?php echo (count($downloads) - 1); ?>);

				/*var zm = $('<div class="zoom-controls"></div>');
				$('<div class="zoom zoom-out" title="zoom out">zoom out</div>')
					.appendTo(zm)
					.on('click', function (event) {
						event.preventDefault();
						chart_users.zoomOut();
					});
				$('<div class="zoom zoom-in" title="zoom in">zoom in</div>')
					.appendTo(zm)
					.on('click', function (event) {
						event.preventDefault();
						chart_users.zoom();
					});
				zm.appendTo(cusers);*/

				var timelineOptions = {
						legend: {
							show: false
						},
						series: {
							points: {
								show: false
							},
							lines: {
								show: true,
								lineWidth: 1,
								fill: false,
								fillColor: 'rgba(0, 0, 0, 0.085)'
							},
							shadowSize: 0
						},
						grid: {
							borderWidth: 1,
							borderColor: 'rgba(0, 0, 0, 0.2)'
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
							},
							tickLength: 0
						},
						selection: {
							mode: "x",
							color: 'rgba(0, 0, 0, 0.3)',
							navigate: true
						}
					};
				var uoTimeline = $("#chart-users-overview")
					.unbind("plotselected")
					.bind("plotselected", function (event, ranges) {
						chart_users.setSelection(ranges);
					})
					.unbind("plotnavigating")
					.bind("plotnavigating", function (event, ranges) {
						chart_users.setSelection(ranges);
					});
				var plotUO = $.plot(uoTimeline, datasets, timelineOptions);
				plotUO.setSelection({
						xaxis: {
							from: new Date('<?php echo $from; ?>'),
							to: new Date('<?php echo $to; ?>')
						}
					},
					true
				);


				var dataset_sim_users = [
					{
						color: "#666666", //#CFCFAB", //#CFCFAB
						label: "<?php echo Lang::txt('PLG_USAGE_OVERVIEW_SIMULATION_USERS'); ?>",
						data: [<?php echo implode(',', $simusers); ?>]
					}
				];

				var csimusers = $('#chart-sim-users');
				csimusers.bind("plotclick", function(event, pos, item) {
					return updateCharts(event, pos, item);
				});

				var chart_sim_users = $.plot(csimusers, dataset_sim_users, {
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
						//tickLength: 0,
						tickDecimals: 0,
						<?php if (count($simusers) <= 12) { echo 'ticks: ' . count($simusers) . ','; } ?>
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							//return month_short[d.getUTCMonth()];//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
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
				chart_sim_users.highlight(0, <?php echo (count($simusers) - 1); ?>);

				var dataset_sim_jobs = [
					{
						color: "#666666", //#CFCFAB
						label: "<?php echo Lang::txt('PLG_USAGE_OVERVIEW_SIMULATION_JOBS'); ?>",
						data: [<?php echo implode(',', $simjobs); ?>]
					}
				];

				var csimjobs = $('#chart-sim-jobs');
				csimjobs.bind("plotclick", function(event, pos, item) {
					return updateCharts(event, pos, item);
				});

				var chart_sim_jobs = $.plot(csimjobs, dataset_sim_jobs, {
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
						//tickLength: 0,
						tickDecimals: 0,
						<?php if (count($simjobs) <= 12) { echo 'ticks: ' . count($simjobs) . ','; } ?>
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							//return month_short[d.getUTCMonth()];//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
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
				chart_sim_jobs.highlight(0, <?php echo (count($simjobs) - 1); ?>);
			});
		}
	</script>
</section>