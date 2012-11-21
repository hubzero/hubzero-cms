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
?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>"><?php echo JText::_('Tickets'); ?></a></li>
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>"><?php echo JText::_('SUPPORT_NEW_TICKET'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div id="sub-menu">
	<ul>
		<li id="sm-1"<?php if ($this->type == 0) { echo ' class="active"'; } ?>><a class="tab" rel="submitted" href="/support/stats"><span>Submitted Tickets</span></a></li>
		<li id="sm-2"<?php if ($this->type == 1) { echo ' class="active"'; } ?>><a class="tab" rel="automatic" href="/support/stats?type=automatic"><span>Automatic Tickets</span></a></li>
	</ul>
	<div class="clear"></div>
</div><!-- / #sub-menu -->

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>" method="get" enctype="multipart/form-data">
<div class="main section" id="ticket-stats">
	<h3>Overview</h3>

	<fieldset class="filters">
		<label>
			<?php echo JText::_('Year'); ?>: 
			<select name="year">
<?php
			$y = date("Y");
			$year = $y;
			$y++;
			for ($i=2004, $n=$y; $i < $n; $i++)
			{
?>
				<option value="<?php echo $i; ?>"<?php if ($this->year == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
<?php
			}
?>
			</select>
		</label>
		
		<label>
			<?php echo JText::_('Group'); ?>: 
			<?php 
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher =& JDispatcher::getInstance();
		$gc = $dispatcher->trigger( 'onGetSingleEntry', array(array('groups', 'group', 'acgroup','',$this->group)) );
		if (count($gc) > 0) {
			echo $gc[0];
		} else { ?>
			<input type="text" name="group" value="<?php echo $this->group; ?>" id="acgroup" size="35" autocomplete="off" />
		<?php } ?>
		</label>
		<input type="submit" value="View" />
	</fieldset>

	<table class="support-stats-overview open-tickets" summary="Overview of open support tickets">
		<thead>
			<tr>
				<th scope="col">Opened this year</th>
				<th scope="col">Opened this month</th>
				<th scope="col">Opened this week</th>
				<th scope="col">Open</th>
				<th scope="col">Unassigned</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $this->opened['year']; ?></td>
				<td><?php echo ($year == $this->year) ? $this->opened['month'] : '-'; ?></td>
				<td><?php echo ($year == $this->year) ? $this->opened['week'] : '-'; ?></td>
				<td class="major"><?php echo $this->opened['open']; ?></td>
				<td class="critical"><?php echo $this->opened['unassigned']; ?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="support-stats-overview closed-tickets" summary="Overview of closed support tickets">
		<thead>
			<tr>
				<th scope="col">Closed this year</th>
				<th scope="col">Closed this month</th>
				<th scope="col">Closed this week</th>
				<th scope="col" colspan="2" class="block">Average lifetime</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $this->closed['year']; ?></td>
				<td><?php echo ($year == $this->year) ? $this->closed['month'] : '-'; ?></td>
				<td><?php echo ($year == $this->year) ? $this->closed['week'] : '-'; ?></td>
				<td colspan="2" class="block"><?php echo (isset($this->lifetime[0])) ? $this->lifetime[0] : 0; ?> <span>days</span> <?php echo (isset($this->lifetime[1])) ? $this->lifetime[1] : 0; ?> <span>hours</span> <?php echo (isset($this->lifetime[2])) ? $this->lifetime[2] : 0; ?> <span>minutes</span></td>
			</tr>
		</tbody>
	</table>
	
	<div class="aside">
		<h3>Tickets Submitted (red) vs. Closed (green)</h3>
		<canvas id="line1" width="475" height="250">[Please wait...]</canvas>
		
		<!-- <h3>Ticket Total</h3>
		<canvas id="line2" width="475" height="250">[Please wait...]</canvas> -->
		<script type="text/javascript" src="/components/com_support/assets/js/rgraph/RGraph.common.js" ></script>
		<script type="text/javascript" src="/components/com_support/assets/js/rgraph/RGraph.line.js" ></script>
		<!--[if IE]><script src="/components/com_support/assets/js/excanvas/excanvas.compressed.js"></script><![endif]-->
<?php
if ($this->type == 1) {
	$type = 'automatic';
}

$closeddata = '';
if ($this->closedmonths) {
	$closeddata = implode(',',$this->closedmonths);
}
$mcd = max($this->closedmonths);

$openeddata = '';
if ($this->openedmonths) {
	foreach ($this->openedmonths as $k=>$v)
	{
		$o[$k] = $this->openedmonths[$k]; // - $this->closedmonths[$k];
	}
	$openeddata = implode(',',$o);
}
$ocd = max($this->openedmonths);

$number = max($ocd, $mcd);
$max = ceil($number/10)*10;
?>
		<script type="text/javascript">
		window.onload = function ()
		{
			var line1 = new RGraph.Line('line1', [<?php echo $openeddata; ?>], [<?php echo $closeddata; ?>]);
			line1.Set('chart.colors', ['red', 'green']);
			line1.Set('chart.tickmarks', 'circle');
			line1.Set('chart.linewidth', 1);
			line1.Set('chart.background.barcolor1', 'white');
			line1.Set('chart.background.barcolor2', 'white');
			//line1.Set('chart.filled', 'true');
			//line1.Set('chart.fillstyle', ['rgba(255,130,130,0.5)','rgba(128,255,128,0.5)']);
			line1.Set('chart.text.angle', 45);
			line1.Set('chart.text.color', '#777777');
			line1.Set('chart.gutter', 35);
			line1.Set('chart.noaxes', true);
			line1.Set('chart.background.grid', true);
			line1.Set('chart.background.grid.vsize', 36.9);
			line1.Set('chart.yaxispos', 'left');
			line1.Set('chart.ymax', <?php echo $max; ?>);
			line1.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
			line1.Draw();
			
			/*var line2 = new RGraph.Line('line2', [20,25,13,14,16,18,21,32,12,15,0,0]);
			line2.Set('chart.colors', ['red']);
			line2.Set('chart.tickmarks', 'circle');
			line2.Set('chart.linewidth', 1);
			line2.Set('chart.background.barcolor1', 'white');
			line2.Set('chart.background.barcolor2', 'white');
			line2.Set('chart.filled', 'true');
			line2.Set('chart.fillstyle', ['#fcc']);
			line2.Set('chart.text.angle', 45);
			line2.Set('chart.text.color', '#777777');
			line2.Set('chart.gutter', 35);
			line2.Set('chart.noaxes', true);
			line2.Set('chart.background.grid', true);
			line2.Set('chart.yaxispos', 'left');
			line2.Set('chart.ymax', 100);
			line2.Set('chart.background.grid.vsize', 36.9);
			line2.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
			line2.Draw();*/
		}
		</script>
	</div>
	
	<div class="subject">
		<h3>People</h3>
		<table class="support-stats-people" summary="Breakdown of people and the number of tickets closed">
			<thead>
				<tr>
					<th scope="col"><a<?php if ($this->sort == 'name') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&amp;task=stats&amp;type='.$this->type.'&amp;sort=name&amp;group='.$this->group.'&amp;year='.$this->year); ?>" title="Sort by name">&darr; Person</a></th>
					<th scope="col"><a<?php if ($this->sort == 'year') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&amp;task=stats&amp;type='.$this->type.'&amp;sort=year&amp;group='.$this->group.'&amp;year='.$this->year); ?>" title="Sort by year count">&darr; Closed this year</a></th>
					<th scope="col"><a<?php if ($this->sort == 'month') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&amp;task=stats&amp;type='.$this->type.'&amp;sort=month&amp;group='.$this->group.'&amp;year='.$this->year); ?>" title="Sort by month count">&darr; Closed this month</a></th>
					<th scope="col"><a<?php if ($this->sort == 'week') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&amp;task=stats&amp;type='.$this->type.'&amp;sort=week&amp;group='.$this->group.'&amp;year='.$this->year); ?>" title="Sort by week count">&darr; Closed this week</a></th>
				</tr>
			</thead>
			<tbody>
<?php
if ($this->users) {
	$cls = 'even';
	foreach ($this->users as $user)
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';
?>
				<tr class="<?php echo $cls; ?>">
					<th scope="row"><?php echo stripslashes($user->name); ?></th>
					<td class="group"><?php echo $user->closed['year']; ?></td>
					<td><?php echo ($year == $this->year) ? $user->closed['month'] : '-'; ?></td>
					<td class="group"><?php echo ($year == $this->year) ? $user->closed['week'] : '-'; ?></td>
				</tr>
<?php
	}
}
?>
			</tbody>
		</table>
	</div>
	<div class="clear"></div>
</div><!-- / .section -->
</form>
