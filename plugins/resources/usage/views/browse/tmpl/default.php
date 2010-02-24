<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

if ($this->resource->alias) {
	$url = 'index.php?option='.$this->option.'&alias='.$this->resource->alias.'&active=usage';
} else {
	$url = 'index.php?option='.$this->option.'&id='.$this->resource->id.'&active=usage';
}

$img1 = $this->chart_path.$this->dthis.'-'.$this->period.'-'.$this->resource->id.'-Users.gif';
$img2 = $this->chart_path.$this->dthis.'-'.$this->period.'-'.$this->resource->id.'-Jobs.gif';

$cls = 'even';

$database =& JFactory::getDBO();

$topvals = new ResourcesStatsToolsTopvals( $database );
?>
<h3>
	<a name="usage"></a>
	<?php echo JText::_('PLG_RESOURCES_USAGE'); ?> 
</h3>
<div id="sub-sub-menu">
	<ul>
		<li<?php if ($this->period == '14') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=14&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_RESOURCES_USAGE_PERIOD_OVERALL'); ?></span></a></li>
		<li<?php if ($this->period == 'prior12' || $this->period == '12') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=12&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_RESOURCES_USAGE_PERIOD_PRIOR12'); ?></span></a></li>
		<li<?php if ($this->period == 'month' || $this->period == '1') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=1&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_RESOURCES_USAGE_PERIOD_MONTH'); ?></span></a></li>
		<li<?php if ($this->period == 'qtr' || $this->period == '3') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=3&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_RESOURCES_USAGE_PERIOD_QTR'); ?></span></a></li>
		<li<?php if ($this->period == 'year' || $this->period == '0') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=0&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_RESOURCES_USAGE_PERIOD_YEAR'); ?></span></a></li>
		<li<?php if ($this->period == 'fiscal' || $this->period == '13') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=13&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_RESOURCES_USAGE_PERIOD_FISCAL'); ?></span></a></li>
	</ul>
</div>
<form method="get" action="<?php echo JRoute::_($url); ?>">
	<div class="timeperiod">
		<fieldset>
			<label>
				<?php echo JText::_('PLG_RESOURCES_USAGE_TIME_PERIOD'); ?>
				<?php echo plgResourcesUsage::dropDownDates( $database, $this->period, $this->resource->id, $this->dthis ); ?>
			</label>
			<input type="submit" value="<?php echo JText::_('PLG_RESOURCES_USAGE_GO'); ?>" />
		</fieldset>
	</div>
	<div id="statistics">
<?php if ((is_file(JPATH_ROOT.$img1) && !is_file(JPATH_ROOT.$img2)) || (!is_file(JPATH_ROOT.$img1) && is_file(JPATH_ROOT.$img2))) { ?>
		<div class="two columns first">
<?php } ?>
		<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_1_CAPTION'); ?>">
			<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_1_CAPTION'); ?></caption>
			<thead>
				<tr>
					<th scope="col" class="textual-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_ITEM'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_AVERAGE'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TOTAL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?>:</th>
					<td>-</td>
					<td><?php echo number_format($this->stats->users); ?></td>
				</tr>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_INTERACTIVE_SESSIONS'); ?>:</th>
					<td>-</td>
					<td><?php echo number_format($this->stats->sessions); ?></td>
				</tr>
<?php 
$i = 0;
$img = $this->chart_path.$this->dthis.'-'.$this->period.'-'.$this->resource->id.'-Simulations.gif';
if ($this->stats->simulations == $this->stats->jobs) { ?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
<?php if (is_file(JPATH_ROOT.$img)) { ?>
					<th scope="row">
						<a href="<?php echo $img; ?>" title="DOM:users1<?php echo $i; ?>" class="fixedResourceTip" rel="external"><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_SESSIONS'); ?>:</a>
						<div style="display:none;" id="users1<?php echo $i; ?>"><img src="<?php echo $img; ?>" alt="" /></div>
					</th>
<?php } else { ?>
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_SESSIONS'); ?>:</th>
<?php } ?>
					<td>-</td>
					<td><?php echo number_format($this->stats->simulations); ?></td>
				</tr>
<?php } else { ?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
<?php if (is_file(JPATH_ROOT.$img)) { ?>
					<th scope="row">
						<a href="<?php echo $img; ?>" title="DOM:users1<?php echo $i; ?>" class="fixedResourceTip" rel="external"><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_SESSIONS'); ?>:</a>
						<div style="display:none;" id="users1<?php echo $i; ?>"><img src="<?php echo $img; ?>" alt="" /></div>
					</th>
<?php } else { ?>
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_SESSIONS'); ?>:</th>
<?php } ?>
					<td>-</td>
					<td><?php echo number_format($this->stats->simulations); ?></td>
				</tr>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?>:</th>
					<td>-</td>
					<td><?php echo number_format($this->stats->jobs); ?></td>
				</tr>
<?php } ?>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_WALL_TIME'); ?>:</th>
					<td><?php echo plgResourcesUsage::timeUnits($this->stats->avg_wall); ?></td>
					<td><?php echo plgResourcesUsage::timeUnits($this->stats->tot_wall); ?></td>
				</tr>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_CPU_TIME'); ?>:</th>
					<td><?php echo plgResourcesUsage::timeUnits($this->stats->avg_cpu); ?></td>
					<td><?php echo plgResourcesUsage::timeUnits($this->stats->tot_cpu); ?></td>
				</tr>
				<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
					<th scope="row"><?php echo JText::_('PLG_RESOURCES_USAGE_INTERACTION_TIME'); ?>:</th>
					<td><?php echo plgResourcesUsage::timeUnits($this->stats->avg_view); ?></td>
					<td><?php echo plgResourcesUsage::timeUnits($this->stats->tot_view); ?></td>
				</tr>
			</tbody>
		</table>
<?php if (is_file(JPATH_ROOT.$img1) && is_file(JPATH_ROOT.$img2)) { ?>
		<div class="two columns first">
			<p style="text-align: center;"><img src="<?php echo $img1; ?>" alt="" /></p>
		</div>
		<div class="two columns second">
			<p style="text-align: center;"><img src="<?php echo $img2; ?>" alt="" /></p>
		</div>
<?php } else if ((is_file(JPATH_ROOT.$img1) && !is_file(JPATH_ROOT.$img2)) || (!is_file(JPATH_ROOT.$img1) && is_file(JPATH_ROOT.$img2))) { ?>
		</div>
		<div class="two columns second">
<?php if (is_file(JPATH_ROOT.$img1)) { ?>
			<p style="text-align: center;"><img src="<?php echo $img1; ?>" alt="" /></p>
<?php } else { ?>
			<p style="text-align: center;"><img src="<?php echo $img2; ?>" alt="" /></p>
<?php } ?>
		</div>
<?php } ?>
		<div class="clear"></div>

		<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?>">
			<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?></caption>
			<thead>
				<tr>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TYPE'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
$toporgs = $topvals->getTopCountryRes( $this->stats->id, 3 );
if ($toporgs) {
	$total = '';
	$cls = 'even';
	$tot = '';
	foreach ($toporgs as $row) 
	{
		if ($row->name == '?') {
			$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

		if ($row->rank == '0') {
			$total = $row->value;
			if ($total) {
				$tot = '<tr class="summary">
					<td> </td>
					<td class="textual-data">'.$row->name.'</td>
					<td>'.number_format($row->value).'</td>
					<td>'.round((($row->value/$total)*100),2).'</td>
				</tr>';
			}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
				<tr class="<?php echo $cls; ?>">
					<td><?php echo $row->rank; ?></td>
					<td class="textual-data"><?php echo $row->name; ?></td>
					<td><?php echo number_format($row->value); ?></td>
					<td><?php echo round((($row->value/$total)*100),2); ?></td>
				</tr>
<?php
		}
	}
}
echo $tot;
?>
			</tbody>
		</table>
		<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?>">
			<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?></caption>
			<thead>
				<tr>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_COUNTRY'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
$topcountries = $topvals->getTopCountryRes( $this->stats->id, 1 );
if ($topcountries) {
	$total = '';
	$cls = 'even';
	$tot = '';
	foreach ($topcountries as $row) 
	{
		if ($row->name == '?') {
			$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

		if ($row->rank == '0') {
			$total = $row->value;
			if ($total) {
				$tot = '<tr class="summary">
					<td> </td>
					<td class="textual-data">'.$row->name.'</td>
					<td>'.number_format($row->value).'</td>
					<td>'.round((($row->value/$total)*100),2).'</td>
				</tr>';
			}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
				<tr class="<?php echo $cls; ?>">
					<td><?php echo $row->rank; ?></td>
					<td class="textual-data"><?php echo $row->name; ?></td>
					<td><?php echo number_format($row->value); ?></td>
					<td><?php echo round((($row->value/$total)*100),2); ?></td>
				</tr>
<?php
		}
	}
}
echo $tot;
?>
			</tbody>
		</table>
		<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?>">
			<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?></caption>
			<thead>
				<tr>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_DOMAINS'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
$topdoms = $topvals->getTopCountryRes( $this->stats->id, 2 );
if ($topdoms) {
	$total = '';
	$cls = 'even';
	$tot = '';
	foreach ($topdoms as $row) 
	{
		if ($row->name == '?') {
			$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

		if ($row->rank == '0') {
			$total = $row->value;
			if ($total) {
				$tot = '<tr class="summary">
					<td> </td>
					<td class="textual-data">'.$row->name.'</td>
					<td>'.number_format($row->value).'</td>
					<td>'.round((($row->value/$total)*100),2).'</td>
				</tr>';
			}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
				<tr class="<?php echo $cls; ?>">
					<td><?php echo $row->rank; ?></td>
					<td class="textual-data"><?php echo $row->name; ?></td>
					<td><?php echo number_format($row->value); ?></td>
					<td><?php echo round((($row->value/$total)*100),2); ?></td>
				</tr>
<?php
		}
	}
}
echo $tot;
?>
			</tbody>
		</table>

<?php
$juser =& JFactory::getUser();
if (!$juser->get('guest')) {
	// Check if they're a site admin (from Joomla)
	if ($juser->authorize($this->option, 'manage')) {
		$topvalsusers = new ResourcesStatsToolsUsers($database);
		$topusers = $topvalsusers->getTopUsersRes($this->resource->id, $this->dthis, $this->period, '3');
?>
		<table summary="<?php echo JText::_('PLG_RESOURCES_USAGE_TBL_5_CAPTION'); ?>">
			<caption><?php echo JText::_('PLG_RESOURCES_USAGE_TBL_5_CAPTION'); ?></caption>
			<thead>
				<tr>
					<th scope="col" class="numerical-data"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_NUM'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_USER'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_ORGANIZATION'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_EMAIL'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_INTERACTIVE_SESSIONS'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_SIMULATION_SESSIONS'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_SIMULATION_RUNS'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TOTAL_WALL_TIME'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TOTAL_CPU_TIME'); ?></th>
					<th scope="col"><?php echo JText::_('PLG_RESOURCES_USAGE_COL_TOTAL_INTERACTION_TIME'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
		if ($topusers) {
			$cls = 'even';
			$rank = 1;
			foreach ($topusers as $row) 
			{
				if ($row->name == '?') {
					$row->name = JText::_('PLG_RESOURCES_USAGE_UNIDENTIFIED');
				}

				$cls = ($cls == 'even') ? 'odd' : 'even';
?>
				<tr class="<?php echo $cls; ?>">
					<td><?php echo $rank; ?></td>
					<td class="textual-data"><?php echo $row->name; ?> (<?php echo $row->user; ?>)</td>
					<td class="textual-data"><?php echo $row->organization; ?></td>
					<td class="textual-data"><?php echo $row->email; ?></td>
					<td><?php echo $row->sessions; ?></td>
					<td><?php echo number_format($row->simulations); ?></td>
					<td><?php echo number_format($row->jobs); ?></td>
					<td><?php echo plgResourcesUsage::timeUnits($row->tot_wall); ?></td>
					<td><?php echo plgResourcesUsage::timeUnits($row->tot_cpu); ?></td>
					<td><?php echo plgResourcesUsage::timeUnits($row->tot_view); ?></td>
				</tr>
<?php
				$rank++;
			}
		} else { 
?>
				<tr class="odd">
					<td colspan="10" class="textual-data"><?php echo JText::_('PLG_RESOURCES_USAGE_NO_DATA_AVAILABLE'); ?></td>
				</tr>
<?php
		} 
?>
			</tbody>
		</table>
<?php
	}
}

$tool_map = $this->map_path.$this->resource->id;
if (file_exists(JPATH_ROOT.$tool_map.'.gif')) {
?>
		<p><?php echo JText::sprintf('PLG_RESOURCES_USAGE_MAP_EXPLANATION',$this->resource->title); ?></p>
		<p><a href="<?php echo $tool_map; ?>.png" title="<?php echo JText::_('PLG_RESOURCES_USAGE_MAP_LARGER'); ?>"><img src="<?php echo $tool_map; ?>.gif" alt="<?php echo JText::_('PLG_RESOURCES_USAGE_MAP'); ?>" /></a></p>
<?php
}
?>
	</div><!-- / #statistics -->
</form>