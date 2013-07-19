<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
defined('_JEXEC') or die('Restricted access');

// Set date time format
$dateFormat = '%d %b %Y';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = true;
}

// Push scripts to document
ximport('Hubzero_Document');
Hubzero_Document::addPluginStylesheet('resources', 'usage');
$document = JFactory::getDocument();
if (!JPluginHelper::isEnabled('system', 'jquery'))
{
	$document->addScript(DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.min.js');
}
$document->addScript(DS . 'media' . DS . 'system' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.min.js');
$document->addScript(DS . 'media' . DS . 'system' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.selection.js');
$document->addScript(DS . 'media' . DS . 'system' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.resize.min.js');
$document->addScript(DS . 'media' . DS . 'system' . DS . 'js' . DS . 'flot' . DS . 'jquery.flot.crosshair.min.js');

// Set the base URL
if ($this->resource->alias) {
	$url = 'index.php?option=' . $this->option . '&alias=' . $this->resource->alias . '&active=usage';
} else {
	$url = 'index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=usage';
}

$img1 = $this->chart_path . $this->dthis . '-' . $this->period . '-' . $this->resource->id . '-Users.gif';
$img2 = $this->chart_path . $this->dthis . '-' . $this->period . '-' . $this->resource->id . '-Jobs.gif';

$cls = 'even';

$database =& JFactory::getDBO();

$topvals = new ResourcesStatsToolsTopvals($database);

if (intval($this->params->get('cache', 1)))
{
	$cache =& JFactory::getCache('callback');
	$cache->setCaching(1);
	$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
	$results = $cache->call(array('plgResourcesUsage', 'getOverview'), $this->resource->id);
}
else 
{
	$results = plgResourcesUsage::getOverview($this->resource->id);
}

$users = array();
$interactive = array();
$sessions = array();
$runs = array();

$usersScaled = array();
$runsScaled = array();

$min = (date("Y") - 1) . '/' . date("m") . '/01';
$to = $max = date("Y") . '/' . date("m") . '/01';
$from = (date("Y") - 1) . '/' . date("m") . '/01';
$half = date('Y/m/d', mktime(0, 0, 0, (date("m") - 6), 1, date("Y")));
$qrtr = date('Y/m/d', mktime(0, 0, 0, (date("m") - 3), 1, date("Y")));

if ($results)
{
	$usersTop = 0;
	$runsTop = 0;
	
	$c = count($results);
	foreach ($results as $result)
	{
		$users[]       = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->users . "]";
		$interactive[] = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->sessions . "]";
		$sessions[]    = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->simulations . "]";
		$runs[]        = "[new Date('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . "')," . $result->jobs . "]";

		$usersTop = ($result->users > $usersTop) ? $result->users : $usersTop;
		$runsTop = ($result->jobs > $runsTop) ? $result->jobs : $runsTop;
	}

	$min = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $results[0]->datetime));

	$current = end($results);
}
$current->datetime =  str_replace('-00 00:00:00', '-01', $current->datetime);
?>
	<h3 id="plg-usage-header">
		<a name="usage"></a>
		<?php echo JText::_('PLG_RESOURCES_USAGE'); ?> 
	</h3>
	<form method="get" action="<?php echo JRoute::_($url); ?>">
	<?php
	$tool_map = '/site/stats/resource_maps/' . $this->resource->id;
	if (file_exists(JPATH_ROOT . $tool_map . '.gif')) { ?>
		<div id="geo-overview-wrap" class="usage-wrap">
			<div class="four columns first">
				<h4><?php echo JText::_('World usage'); ?></h4>
				<p><?php echo JText::sprintf('PLG_RESOURCES_USAGE_MAP_EXPLANATION', stripslashes($this->resource->title)); ?></p>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<p>
					<a href="<?php echo $tool_map; ?>.png" title="<?php echo JText::_('PLG_RESOURCES_USAGE_MAP_LARGER'); ?>">
						<img style="width:100%;max-width:510px;" src="<?php echo $tool_map; ?>.gif" alt="<?php echo JText::_('PLG_RESOURCES_USAGE_MAP'); ?>" />
					</a>
				</p>
			</div><!-- / .four columns second third fourth -->
			<div style="clear:left;"></div>
		</div>
	<?php } ?>

	<?php if ($results) { ?>
		<div id="user-overview-wrap" class="usage-wrap">
			<ul class="dataset-controls" id="set-data">
				<li>
					<a id="monthly" class="dataset active" href="/index.php?option=com_resources&amp;id=<?php echo $this->resource->id; ?>&amp;active=usage&amp;action=overview&amp;period=1">
						Monthly
					</a>
				</li>
				<li>
					<a id="yearly" class="dataset" href="/index.php?option=com_resources&amp;id=<?php echo $this->resource->id; ?>&amp;active=usage&amp;action=overview&amp;period=12">
						Yearly
					</a>
				</li>
				<li>
					<a id="cumulative" class="dataset" href="/index.php?option=com_resources&amp;id=<?php echo $this->resource->id; ?>&amp;active=usage&amp;action=overview&amp;period=14">
						Cumulative
					</a>
				</li>
			</ul>
			<div class="four columns first">
				<h4><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?></h4>
				<p class="total">
					<strong id="users-overview-total"><?php echo number_format($current->users); ?></strong>
					<span><?php echo JText::_('in'); ?> <span id="users-overview-date"><time datetime="<?php echo $current->datetime; ?>"><?php echo JHTML::_('date', $current->datetime, $dateFormat, $tz); ?></time></span></span>
				</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<p class="zoom-controls" id="set-selection-users">
					<?php echo JText::_('Zoom'); ?>
					<a class="set-selection selected" rel="<?php echo $from; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=12&dthis=' . $this->dthis); ?>" title="<?php echo JText::_('Year to date'); ?>"><?php echo JText::_('1y'); ?></a>
					<a class="set-selection" rel="<?php echo $half; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=13&dthis=' . $this->dthis); ?>" title="<?php echo JText::_('6 months'); ?>"><?php echo JText::_('6m'); ?></a>
					<a class="set-selection" rel="<?php echo $qrtr; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=3&dthis=' . $this->dthis); ?>" title="<?php echo JText::_('3 months'); ?>"><?php echo JText::_('3m'); ?></a>
				</p>
				<div id="users-overview" style="min-width:400px;height:250px;">
				<?php
				if ($results) 
				{
					// Find the highest value
					$vals = array();
					foreach ($results as $result)
					{
						$vals[] = $result->users;
					}
					asort($vals);

					$highest = array_pop($vals);

					$sparkline  = '<span class="sparkline">' . "\n";
					foreach ($results as $result)
					{
						$height = ($highest) ? round(($result->users / $highest)*100) : 0;
						$sparkline .= "\t" . '<span class="index">';
						$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . JHTML::_('date', $result->datetime, $dateFormat, $tz) . ': ' . number_format($result->users) . '">';
						$sparkline .= number_format($result->users); //trim($this->_fmt_result($result->value, $result->valfmt));
						$sparkline .= '</span> ';
						$sparkline .= '</span>' . "\n";
					}
					$sparkline .= '</span>' . "\n";
					echo $sparkline;
				}
				?>
				</div>
				<div id="users-overview-timeline" style="min-width:400px;height:100px;margin-top: -7px">
					<!-- blank -->
				</div>

				<div class="two columns first">
				<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?>" id="pie-org-data" class="pie-chart">
					<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?></caption>
					<thead>
						<tr>
							<!-- <th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th> -->
							<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TYPE'); ?></th>
							<th scope="col" colspan="2" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
				$colors = array(
					$this->params->get('pie_chart_color1', '#7c7c7c'),
					$this->params->get('pie_chart_color2', '#515151'),
					$this->params->get('pie_chart_color3', '#d9d9d9'),
					$this->params->get('pie_chart_color4', '#3d3d3d'),
					$this->params->get('pie_chart_color5', '#797979'),
					$this->params->get('pie_chart_color6', '#595959'),
					$this->params->get('pie_chart_color7', '#e5e5e5'),
					$this->params->get('pie_chart_color8', '#828282'),
					$this->params->get('pie_chart_color9', '#404040'),
					$this->params->get('pie_chart_color10', '#6a6a6a'),
					$this->params->get('pie_chart_color1', '#bcbcbc'),
					$this->params->get('pie_chart_color2', '#515151'),
					$this->params->get('pie_chart_color3', '#d9d9d9'),
					$this->params->get('pie_chart_color4', '#3d3d3d'),
					$this->params->get('pie_chart_color5', '#797979'),
					$this->params->get('pie_chart_color6', '#595959'),
					$this->params->get('pie_chart_color7', '#e5e5e5'),
					$this->params->get('pie_chart_color8', '#828282'),
					$this->params->get('pie_chart_color9', '#404040'),
					$this->params->get('pie_chart_color10', '#3a3a3a'),
				);

				$datetime = str_replace('-00 00:00:00', '', $current->datetime);

				$tid = plgResourcesUsage::getTid($this->resource->id, $datetime);


				if (intval($this->params->get('cache', 1)))
				{
					$cache =& JFactory::getCache('callback');
					$cache->setCaching(1);
					$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
					$dataset = $cache->call(array('plgResourcesUsage', 'getTopValue'), $this->resource->id, 3, $tid, $datetime);
				}
				else 
				{
					$dataset = plgResourcesUsage::getTopValue($this->resource->id, 3, $tid, $datetime);

				}
				//$data = array();
				$r = array();
				//$results = null;
				$total = 0;
				$cls = 'even';
				//$tot = '';
				//$pieOrg = array();
				//$toporgs = null;
				if ($dataset)
				{
					$i = 0;
					//$data = array();
					$r = array();

					foreach ($dataset as $row)
					{
						$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));

						//if (!isset($data[$ky]))
						if (!isset($r[$ky]))
						{
							$i = 0;
							//$data[$ky] = array();
							$r[$ky] = array();
						}

						//$data[$ky][] = $row;

						if (!isset($colors[$i]))
						{
							$i = 0;
						}
						$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}';

						if ($row->rank != '0') 
						{
							$total += $row->value;
						}

						$i++;
					}

					$i = 0;
					$total = ($total) ? $total : 1;
					foreach ($dataset as $row)
					{
						if ($row->rank == '0') 
						{
							continue;
						}

						if ($row->name == '?') 
						{
							$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
						}

						$cls = ($cls == 'even') ? 'odd' : 'even';
						?>
						<tr rel="<?php echo $row->name; ?>">
							<!-- <th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th> -->
							<td class="textual-data"><?php echo $row->name; ?></td>
							<td><span class="bar-wrap"><span class="bar" style="width: <?php echo round((($row->value/$total)*100),2); ?>%;"></span><span class="value"><?php echo number_format($row->value); ?> (<?php echo round((($row->value/$total)*100),2); ?>)</span></span></td>
							<!-- <td><?php echo round((($row->value/$total)*100),2); ?></td> -->
						</tr>
						<?php
						$i++;
					}
				}
				else 
				{
				?>
						<tr>
							<td colspan="3" class="textual-data"><?php echo JText::sprintf('No data found for the month of %s', $datetime); ?></td>
						</tr>
				<?php
				}
				?>
					</tbody>
				</table>
				<script type="text/javascript">
					var orgData = {
						<?php
						$z = array();
						foreach ($r as $k => $d)
						{
							$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
						}
						echo implode(',', $z);
						?>
					};
				</script>
			</div>
			<div class="two columns second">
				<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?>" id="pie-country-data" class="pie-chart">
					<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?></caption>
					<thead>
						<tr>
							<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_COUNTRY'); ?></th>
							<th scope="col" colspan="2" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if (intval($this->params->get('cache', 1)))
					{
						$cache =& JFactory::getCache('callback');
						$cache->setCaching(1);
						$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
						$dataset = $cache->call(array('plgResourcesUsage', 'getTopValue'), $this->resource->id, 1, $tid, $datetime);
					}
					else 
					{
						$dataset = plgResourcesUsage::getTopValue($this->resource->id, 1, $tid, $datetime);
					}

					$total = 0;
					$i = 0;
					if ($dataset)
					{
						//$data = array();
						$r = array();
						$names = array();
						foreach ($dataset as $row)
						{
							$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
							//if (!isset($data[$ky]))
							if (!isset($r[$ky]))
							{
								$i = 0;
								//$data[$ky] = array();
								$r[$ky] = array();
							}
							//$data[$ky][] = $row;
							if (!isset($colors[$i]))
							{
								$i = 0;
							}

							$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}'."\n";

							if ($row->rank != '0') 
							{
								$total += $row->value;
							}

							$names[] = $row->name;

							$i++;
						}

						ximport('Hubzero_Geo');
						$codes = Hubzero_Geo::getCodesByNames($names);

						$cls = 'even';
						//$pie = array();
						$i = 0;
						$total = ($total) ? $total : 1;

						foreach ($dataset as $row)
						{
							if ($row->rank == '0') 
							{
								continue;
							}

							if ($row->name == '?') 
							{
								$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
							}

							$cls = ($cls == 'even') ? 'odd' : 'even';
							?>
						<tr rel="<?php echo $row->name; ?>">
							<!-- <th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th> -->
							<td class="textual-data"><?php 
							if (isset($codes[$row->name])) { ?>
								<img src="/components/com_members/assets/img/flags/<?php echo strtolower($codes[$row->name]['code']); ?>.gif" alt="<?php echo strtolower($codes[$row->name]['code']); ?>" /> 
							<?php }
							echo $row->name; ?></td>
							<td><span class="bar-wrap"><span class="bar" style="width: <?php echo round((($row->value/$total)*100),2); ?>%;"></span><span class="value"><?php echo number_format($row->value); ?> (<?php echo round((($row->value/$total)*100),2); ?>%)</span></span></td>
							<!-- <td><?php echo round((($row->value/$total)*100),2); ?>%</td> -->
						</tr>
							<?php
							$i++;
						}
					}
					else 
					{
					?>
						<tr>
							<td colspan="3" class="textual-data"><?php echo JText::sprintf('No data found for the month of %s', $datetime); ?></td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
				<script type="text/javascript">
					var countryData = {
						<?php
						$z = array();
						foreach ($r as $k => $d)
						{
							$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
						}
						echo implode(',', $z);
						?>
					};
				</script>
				</div>
				<?php /*<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?>" id="pie-domains-data" class="pie-chart">
					<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?></caption>
					<thead>
						<tr>
							<!-- <th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th> -->
							<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_DOMAINS'); ?></th>
							<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
							<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if (intval($this->params->get('cache', 1)))
					{
						$cache =& JFactory::getCache('callback');
						$cache->setCaching(1);
						$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
						$results = $cache->call(array('plgResourcesUsage', 'getTopValue'), $this->resource->id, 2, $tid, $datetime);
					}
					else 
					{
						$results = plgResourcesUsage::getTopValue($this->resource->id, 2, $tid, $datetime);
					}

					$total = 0;
					$i = 0;
					if ($results)
					{
						//$data = array();
						$r = array();
						foreach ($results as $row)
						{
							$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
							//if (!isset($data[$ky]))
							if (!isset($r[$ky]))
							{
								$i = 0;
								//$data[$ky] = array();
								$r[$ky] = array();
							}
							//$data[$ky][] = $row;
							if (!isset($colors[$i]))
							{
								$i = 0;
							}
							$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}';

							if ($row->rank != '0') 
							{
								$total += $row->value;
							}

							$i++;
						}

						$cls = 'even';
						$tot = '';

						$i = 0;
						foreach ($results as $row)
						{
							if ($row->rank == '0') 
							{
								continue;
							}

							if ($row->name == '?') 
							{
								$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
							}

							$cls = ($cls == 'even') ? 'odd' : 'even';
							?>
						<tr rel="<?php echo $row->name; ?>">
							<!-- <th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th> -->
							<td class="textual-data"><?php echo $row->name; ?></td>
							<td><?php echo number_format($row->value); ?></td>
							<td><?php echo round((($row->value/$total)*100),2); ?></td>
						</tr>
							<?php
							$i++;
						}
					}
					else 
					{
				?>
						<tr>
							<td colspan="3" class="textual-data"><?php echo JText::sprintf('No data found for the month of %s', $datetime); ?></td>
						</tr>
				<?php
					}
				?>
					</tbody>
				</table>
				<script type="text/javascript">
					var domainData = {
						<?php
						$z = array();
						foreach ($r as $k => $d)
						{
							$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
						}
						echo implode(',', $z);
						?>
					};
				</script>*/ ?>
			</div><!-- / .four columns second third fourth -->
			<div style="clear:left;"></div>
		</div><!-- / #user-overview-wrap -->

		<div id="runs-overview-wrap" class="usage-wrap">
			<div class="four columns first">
				<h4><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?></h4>
				<p class="total">
					<strong id="runs-overview-total"><?php echo number_format($current->jobs); ?></strong>
					<span><?php echo JText::_('in'); ?> <span id="runs-overview-date"><time datetime="<?php echo $current->datetime; ?>"><?php echo JHTML::_('date', $current->datetime, $dateFormat, $tz); ?></time></span></span>
				</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<p class="zoom-controls" id="set-selection-runs">
					<?php echo JText::_('Zoom'); ?>
					<a class="set-selection selected" rel="<?php echo $from; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=12&dthis=' . $this->dthis); ?>" title="<?php echo JText::_('Year to date'); ?>"><?php echo JText::_('1y'); ?></a>
					<a class="set-selection" rel="<?php echo $half; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=13&dthis=' . $this->dthis); ?>" title="<?php echo JText::_('6 months'); ?>"><?php echo JText::_('6m'); ?></a>
					<a class="set-selection" rel="<?php echo $qrtr; ?> <?php echo $to; ?>" href="<?php echo JRoute::_($url . '&period=3&dthis=' . $this->dthis); ?>" title="<?php echo JText::_('3 months'); ?>"><?php echo JText::_('3m'); ?></a>
				</p>
				<div id="runs-overview" style="min-width:400px;height:250px;">
				<?php
					// Find the highest value
					$vals = array();
					foreach ($results as $result)
					{
						$vals[] = $result->jobs;
					}
					asort($vals);

					$highest = array_pop($vals);

					$sparkline  = '<span class="sparkline">' . "\n";
					foreach ($results as $result)
					{
						$height = ($highest) ? round(($result->jobs / $highest)*100) : 0;
						$sparkline .= "\t" . '<span class="index">';
						$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . JHTML::_('date', $result->datetime, $dateFormat, $tz) . ': ' . $result->jobs . '">';
						$sparkline .= number_format($result->jobs); //trim($this->_fmt_result($result->value, $result->valfmt));
						$sparkline .= '</span> ';
						$sparkline .= '</span>' . "\n";
					}
					$sparkline .= '</span>' . "\n";
					echo $sparkline;
				?>
				</div>
				<div id="runs-overview-timeline" style="min-width:400px;height:100px;margin-top: -7px">
					<!-- blank -->
				</div>
				
				<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_1_CAPTION'); ?>" id="pie-runs-data" class="pie-chart">
					<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_1_CAPTION'); ?></caption>
					<thead>
						<tr>
							<th scope="col" class="numerical-data"></th>
							<th scope="col" class="numerical-data"><?php echo JText::_('Average'); ?></th>
							<th scope="col" class="numerical-data"><?php echo JText::_('Total'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>
								<?php echo JText::_('PLG_RESOURCES_USAGE_WALL_TIME'); ?>
							</th>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->avg_wall); ?>
							</td>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->tot_wall); ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo JText::_('PLG_RESOURCES_USAGE_CPU_TIME'); ?>
							</th>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->avg_cpu); ?>
							</td>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->tot_cpu); ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo JText::_('PLG_RESOURCES_USAGE_INTERACTION_TIME'); ?>
							</th>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->avg_view); ?>
							</td>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->tot_view); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div><!-- / .four columns second third fourth -->
			<div style="clear:left;"></div>
		</div><!-- / #runs-overview-wrap -->
		
		<script type="text/javascript">
			if (!jq) {
				var jq = $;
			}
			if (jQuery()) {
				var $ = jq,
					plotU = null,
					plotR = null;

				var month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

				dataurl = '/index.php?option=com_resources&id=<?php echo $this->resource->id; ?>&active=usage&action=top&datetime=';

				function updateTables(yyyy, mm) {
					var dt = yyyy + '/' + mm + '/01';

					$.getJSON(dataurl + yyyy + '-' + mm, function(series){
						if (!orgData[dt]) {
							orgData[dt] = series.orgs[dt];
						}
						if (!countryData[dt]) {
							countryData[dt] = series.countries[dt];
						}
						/*if (!domainData[dt]) {
							domainData[dt] = series.domains[dt];
						}*/

						if (orgData[dt] && orgData[dt].length > 0) {
							populateTable('pie-org-data', orgData[dt]);
						}
						// Update countries pie chart
						if (countryData[dt] && countryData[dt].length > 0) {
							populateTable('pie-country-data', countryData[dt]);
						}
						// Update domains pie chart
						/*if (domainData[dt] && domainData[dt].length > 0) {
							populateTable('pie-domains-data', domainData[dt]);
						}*/
					});
				}

				function populateTable(id, data) {
					var tbl = $('#' + id + ' tbody');

					tbl.empty();

					var footer = data.shift();
					var total = footer['data'];
					total = (total > 0) ? total : 1;

					for (var i=0; i < data.length; i++)
					{
						tbl.append(
							'<tr>' +
								'<td class="textual-data">' + (data[i]['code'] ? '<img src="/components/com_members/assets/img/flags/' + data[i]['code'] + '.gif" alt="' + data[i]['code'] + '" /> ' : '') + data[i]['label'] + '</td>' + 
								'<td><span class="bar-wrap"><span class="bar" style="width: ' + Math.round(((data[i]['data']/total)*100),2) + '%;"></span><span class="value">' + data[i]['data'] + ' (' + Math.round(((data[i]['data']/total)*100),2) + '%)</span></span></td>' + 
								//'<td>' + Math.round(((data[i]['data']/total)*100),2) + '%</td>' + 
							'</tr>'
						);
					}
					data.unshift(footer);
				}

				$(function () {
					var datasets = [
						{
							lines: { fillColor: '<?php echo "rgba(0, 0, 0, 0.1)"; //$this->params->get("chart_color_fill", "rgba(0, 0, 0, 0.1)"); ?>' },
							color: '<?php echo "#656565"; //$this->params->get("chart_color_line", "#999"); ?>', //#93ACCA
							label: "<?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?>",
							data: [<?php echo implode(',', $users); ?>]
						},
						{
							lines: {fillColor: '<?php echo "rgba(0, 0, 0, 0.1)"; //$this->params->get("chart_color_fill2", "rgba(207, 207, 171, 0.3)"); ?>' },
							color: '<?php echo "#656565"; //$this->params->get("chart_color_line2", "#CFCFAB"); ?>', //#CFCFAB
							label: "<?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?>",
							data: [<?php echo implode(',', $runs); ?>]
						}
					];

					var options = {
						series: {
							lines: { 
								show: true,
								fill: true
							},
							points: { show: true },
							shadowSize: 0
						},
						crosshair: { mode: "x" },
						grid: {
							//color: 'rgba(0, 0, 0, 0.6)',
							borderWidth: 1,
							//borderColor: 'transparent',
							hoverable: true, 
							clickable: true
						},
						legend: { show: false },
						xaxis: { 
							position: 'top', 
							mode: "time", 
							//tickLength: 0, 
							min: new Date('<?php echo $from; ?>'), 
							max: new Date('<?php echo $to; ?>'), 
							tickFormatter: function (val, axis) {
								var d = new Date(val);
								return month_short[d.getUTCMonth()] + " '" + d.getUTCFullYear().toString().substr(2);//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
							},
							tickDecimals: 0 
						},
						yaxis: { min: 0, labelWidth: 25 }
					};

					
					function plotCharts() {
						var placeholderU = $("#users-overview");
						// Bind the selection area so the chart updates
						placeholderU.bind("plotselected", function (event, ranges) {
							if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
								ranges.xaxis.to = ranges.xaxis.from + 0.00001;
							}
							if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
								ranges.yaxis.to = ranges.yaxis.from + 0.00001;
							}
							plotU = $.plot(placeholderU, [datasets[0]],
								$.extend(true, {}, options, {
									xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
								})
							);

							// don't fire event on the overview to prevent eternal loop
							plotUO.setSelection(ranges, true);
						});
						placeholderU.bind("plotclick", function (event, pos, item) {
							if (item) {
								var mm = item.series.data[item.dataIndex][0].getMonth()+1; // January is 0!
								var yyyy = item.series.data[item.dataIndex][0].getFullYear();
								// Prepend 0s
								if (mm < 10) {
									mm = '0' + mm
								}

								$('#users-overview-total').text(item.datapoint[1]);
								$('#runs-overview-total').text(datasets[1].data[item.dataIndex][1]);

								updateTables(yyyy, mm);

								// Unhighlight any previously clicked points
								plotU.unhighlight();
								plotR.unhighlight();
								// Highlight the current point
								plotU.highlight(item.series, item.datapoint);
								plotR.highlight(0, item.dataIndex);
							}
						});
						plotU = $.plot(placeholderU, [datasets[0]], options);


						var timelineOptions = {
							legend: { show: false },
							series: {
								points: { show: false },
								lines: {
									show: true, 
									lineWidth: 1,
									fill: true, 
									fillColor: '<?php echo $this->params->get("chart_color_fill", "rgba(0, 0, 0, 0.085)"); ?>'
								},
								shadowSize: 0
							},
							grid: {
								borderWidth: 1,
								borderColor: 'rgba(0, 0, 0, 0.6)'
							},
							xaxis: { mode: "time", min: new Date('<?php echo $min; ?>'), max: new Date('<?php echo $to; ?>'), tickDecimals: 0, 
								tickFormatter: function (val, axis) {
									var d = new Date(val);
									return month_short[d.getUTCMonth()] + " '" + d.getUTCFullYear().toString().substr(2);//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
								}
							},
							yaxis: { color: 'transparent', min: 0, autoscaleMargin: 0.1, labelWidth: 25 },
							selection: { 
								mode: "x", 
								color: '<?php echo $this->params->get("chart_color_selection", "rgba(0, 0, 0, 0.3)"); ?>', 
								navigate: true 
							}
						};
						


						var placeholderR = $("#runs-overview");
						// Bind the selection area so the chart updates
						placeholderR.bind("plotselected", function (event, ranges) {
							if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
								ranges.xaxis.to = ranges.xaxis.from + 0.00001;
							}
							if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
								ranges.yaxis.to = ranges.yaxis.from + 0.00001;
							}
							plotR = $.plot(placeholderR, [datasets[1]],
								$.extend(true, {}, options, {
									xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
								})
							);

							// don't fire event on the overview to prevent eternal loop
							plotRO.setSelection(ranges, true);
						});
						placeholderR.bind("plotclick", function (event, pos, item) {
							if (item) {
								var mm = item.series.data[item.dataIndex][0].getMonth()+1; // January is 0!
								var yyyy = item.series.data[item.dataIndex][0].getFullYear();
								// Prepend 0s
								if (mm < 10) {
									mm = '0' + mm
								}

								$('#runs-overview-total').text(item.datapoint[1]);
								$('#users-overview-total').text(datasets[0].data[item.dataIndex][1]);

								updateTables(yyyy, mm);

								// Unhighlight any previously clicked points
								plotU.unhighlight();
								plotR.unhighlight();
								// Highlight the current point
								plotR.highlight(item.series, item.datapoint);
								plotU.highlight(0, item.dataIndex);
							}
						});
						plotR = $.plot(placeholderR, [datasets[1]], options);


						var uoTimeline = $("#users-overview-timeline");
						var plotUO = $.plot(uoTimeline, [datasets[0]], timelineOptions);
						plotUO.setSelection({ 
								xaxis: {
									from: new Date('<?php echo $min; ?>'),
									to: new Date('<?php echo $to; ?>')
								}
							}, 
							true
						);
						uoTimeline
							.unbind("plotselected")
							.bind("plotselected", function (event, ranges) {
								plotU.setSelection(ranges);
								ranges.yaxis.to = <?php echo $runsTop; ?>;
								plotR.setSelection(ranges);
							})
							.unbind("plotnavigating")
							.bind("plotnavigating", function (event, ranges) {
								//previousPoint = null;
								plotU.setSelection(ranges);
								ranges.yaxis.to = <?php echo $runsTop; ?>;
								plotR.setSelection(ranges);
							});


						var roTimeline = $("#runs-overview-timeline");
						var plotRO = $.plot(roTimeline, [datasets[1]], timelineOptions);
						plotRO.setSelection({ 
								xaxis: {
									from: new Date('<?php echo $min; ?>'),
									to: new Date('<?php echo $to; ?>')
								}
							}, 
							true
						);
						roTimeline
							.unbind("plotselected")
							.bind("plotselected", function (event, ranges) {
								plotR.setSelection(ranges);
								ranges.yaxis.to = <?php echo $usersTop; ?>;
								plotU.setSelection(ranges);
							})
							.unbind("plotnavigating")
							.bind("plotnavigating", function (event, ranges) {
								//previousPoint = null;
								plotR.setSelection(ranges);
								ranges.yaxis.to = <?php echo $usersTop; ?>;
								plotU.setSelection(ranges);
							});
						
						$('.set-selection').click(function (e) {
							e.preventDefault();

							$('.set-selection').each(function(i, el) {
								$(el).removeClass('selected');
							});
							$(this).addClass('selected');

							var sizeTokens = $(this).attr('rel').split(' ');
							var from = sizeTokens[0];
							var to = sizeTokens[1];

							plotU = $.plot(placeholderU, [datasets[0]],
								$.extend(true, {}, options, {
									xaxis: { min: new Date(from), max: new Date(to) }
								}));
							plotR = $.plot(placeholderR, [datasets[1]],
								$.extend(true, {}, options, {
									xaxis: { min: new Date(from), max: new Date(to) }
								}));

							// don't fire event on the overview to prevent eternal loop
							plotUO.setSelection({
									xaxis: {
										from: new Date(from), 
										to: new Date(to)
									}
								}, 
								true
							);
							plotRO.setSelection({
									xaxis: {
										from: new Date(from), 
										to: new Date(to)
									}
								}, 
								true
							);
						});

						$('a.dataset').on('click', function(e){
							e.preventDefault();

							$('a.dataset').removeClass('active');
							$(this).addClass('active');

							$.getJSON($(this).attr('href'), function(data){
								var runs = [], users = [], runstop = 0, userstop = 0;

								for (var i=0; i<data.points.length; i++) 
								{
									users.push([
										new Date(data.points[i].datetime),
										parseInt(data.points[i].users)
									]);
									runs.push([
										new Date(data.points[i].datetime),
										parseInt(data.points[i].jobs)
									]);
									//userstop = (parseInt(data.points[i].users) > userstop ? parseInt(data.points[i].users) : userstop);
									//runstop  = (parseInt(data.points[i].jobs) > runstop   ? parseInt(data.points[i].jobs)  : runstop);
								}
								datasets[0].data = users;
								datasets[1].data = runs;

								var plotU = $.plot(placeholderU, [datasets[0]], options);
								var plotUO = $.plot(uoTimeline, [datasets[0]], timelineOptions);
								plotUO.setSelection({ 
										xaxis: {
											from: new Date('<?php echo $from; ?>'),
											to: new Date('<?php echo $to; ?>')
										}
									}, 
									true
								);

								var plotR = $.plot(placeholderR, [datasets[1]], options);
								var plotRO = $.plot(roTimeline, [datasets[1]], timelineOptions);
								plotRO.setSelection({ 
										xaxis: {
											from: new Date('<?php echo $from; ?>'),
											to: new Date('<?php echo $to; ?>')
										}
									}, 
									true
								);
							});
							return false;
						});
					}
					$(document).ready(function() {
						plotCharts();
					});

						$(window).resize(function() {
							if (this.resizeTO) clearTimeout(this.resizeTO);
							this.resizeTO = setTimeout(function() {
								$(this).trigger('resizeEnd');
							}, 100);
						});
						$(window).bind('resizeEnd', function() {
							//plotCharts();
							//$('#users-overview div.tickLabel').remove();
							//$('#runs-overview div.tickLabel').remove();
							if (typeof(plotU) != 'undefined') {
								plotU.resize();
								plotU.setupGrid();
								plotU.draw();
							}
							if (typeof(plotR) != 'undefined') {
								plotR.resize();
								plotR.setupGrid();
								plotR.draw();
							}
							
						});

					
				});
			}
		</script>
	<?php } else { ?>
		<div id="no-usage">
			<p><?php echo JText::_('No usage data was found.'); ?></p>
		</div>
	<?php } ?>
	</form>