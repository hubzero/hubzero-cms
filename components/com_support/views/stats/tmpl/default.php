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
?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="ticket" href="/support/tickets"><?php echo JText::_('All Tickets'); ?></a></li>
		<li class="last"><a class="new-ticket" href="/feedback/report_problems/"><?php echo JText::_('SUPPORT_NEW_TICKET'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div id="sub-menu">
	<ul>
		<li id="sm-1"<?php if ($this->type == 0) { echo ' class="active"'; } ?>><a class="tab" rel="submitted" href="/support/stats"><span>Submitted Tickets</span></a></li>
		<li id="sm-2"<?php if ($this->type == 1) { echo ' class="active"'; } ?>><a class="tab" rel="automatic" href="/support/stats?type=automatic"><span>Automatic Tickets</span></a></li>
	</ul>
	<div class="clear"></div>
</div><!-- / #sub-menu -->

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=stats'); ?>" method="get" enctype="multipart/form-data">
<div class="main section" id="ticket-stats">
	<h3>Overview</h3>

	<fieldset class="filters">
		<label>
			<?php echo JText::_('Year'); ?>: 
			<select name="year">
<?php
			$y = date("Y");
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
			<?php echo JText::_('Group'); 
			$document =& JFactory::getDocument();
			$document->addScript(DS.'components'.DS.'com_support'.DS.'observer.js');
			$document->addScript(DS.'components'.DS.'com_support'.DS.'autocompleter.js');
			$document->addStyleSheet(DS.'components'.DS.'com_support'.DS.'autocompleter.css');
			?>:
			<input type="text" name="group" value="<?php echo $this->group; ?>" id="acgroup" value="" size="35" autocomplete="off" />
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
				<td><?php echo $this->opened['month']; ?></td>
				<td><?php echo $this->opened['week']; ?></td>
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
				<td><?php echo $this->closed['month']; ?></td>
				<td><?php echo $this->closed['week']; ?></td>
				<td colspan="2" class="block"><?php echo (isset($this->lifetime[0])) ? $this->lifetime[0] : 0; ?> <span>days</span> <?php echo (isset($this->lifetime[1])) ? $this->lifetime[1] : 0; ?> <span>hours</span> <?php echo (isset($this->lifetime[2])) ? $this->lifetime[2] : 0; ?> <span>minutes</span></td>
			</tr>
		</tbody>
	</table>
	
	<div class="aside">
		<h3>Tickets Submitted (red) vs. Closed (green)</h3>
		<canvas id="line1" width="475" height="250">[Please wait...]</canvas>
		
		<!-- <h3>Ticket Total</h3>
		<canvas id="line2" width="475" height="250">[Please wait...]</canvas> -->
		<script type="text/javascript" src="/components/com_support/scripts/rgraph/RGraph.common.js" ></script>
		<script type="text/javascript" src="/components/com_support/scripts/rgraph/RGraph.line.js" ></script>
		<!--[if IE]><script src="/components/com_support/scripts/excanvas/excanvas.compressed.js"></script><![endif]-->
<?php
$closeddata = '';
if ($this->closedmonths) {
	$closeddata = implode(',',$this->closedmonths);
}
$mcd = max($this->closedmonths);

$openeddata = '';
if ($this->openedmonths) {
	foreach ($this->openedmonths as $k=>$v) 
	{
		$o[$k] = $this->openedmonths[$k] - $this->closedmonths[$k];
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
			line1.Set('chart.filled', 'true');
			//line1.Set('chart.fillstyle', ['#fcc', '#cfc']);
			line1.Set('chart.fillstyle', ['rgba(255,130,130,0.5)','rgba(128,255,128,0.5)']);
			line1.Set('chart.text.angle', 45);
			line1.Set('chart.text.color', '#777777');
			line1.Set('chart.gutter', 35);
			line1.Set('chart.noaxes', true);
			line1.Set('chart.background.grid', true);
			line1.Set('chart.yaxispos', 'left');
			line1.Set('chart.ymax', <?php echo $max; ?>);
			//line1.Set('chart.background.grid.hsize', 20);
			line1.Set('chart.background.grid.vsize', 36.9);
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
					<th scope="col">Person</th>
					<th scope="col">Closed this year</th>
					<th scope="col">Closed this month</th>
					<th scope="col">Closed this week</th>
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
					<td><?php echo $user->closed['month']; ?></td>
					<td class="group"><?php echo $user->closed['week']; ?></td>
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