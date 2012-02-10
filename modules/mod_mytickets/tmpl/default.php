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
defined('_JEXEC') or die( 'Restricted access' );
?>
<div<?php echo ($this->moduleclass) ? ' class="'.$this->moduleclass.'"' : ''; ?>>
	<h4><?php echo JText::_('MOD_MYTICKETS_SUBMITTED'); ?></h4>
<?php
ximport('Hubzero_View_Helper_Html');
$rows1 = $this->rows1;
if (count($rows1) <= 0) {
?>
	<p><?php echo JText::_('MOD_MYTICKETS_NO_TICKETS'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
	foreach ($rows1 as $row)
	{
		$when = $this->timeAgo($row->created);
?>
		<li class="support-ticket <?php echo $row->severity; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_support&task=ticket&id='.$row->id); ?>" class="tooltips" title="#<?php echo $row->id.' :: '.htmlentities(stripslashes($row->summary), ENT_QUOTES); ?>">#<?php echo $row->id.': '.Hubzero_View_Helper_Html::shortenText(stripslashes($row->summary), 35, 0); ?></a>
			<span><span><?php echo $when; ?></span>, <span><?php echo JText::sprintf('MOD_MYTICKETS_COMMENTS',$row->comments); ?></span></span>
		</li>
<?php
	}
?>
	</ul>
<?php } ?>
	<h4><?php echo JText::_('MOD_MYTICKETS_ASSIGNED'); ?></h4>
<?php
$rows2 = $this->rows2;
if (count($rows2) <= 0) {
?>
	<p><?php echo JText::_('MOD_MYTICKETS_NO_TICKETS'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
	foreach ($rows2 as $row)
	{
		$when = $this->timeAgo($row->created);
?>
		<li class="support-ticket <?php echo $row->severity; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_support&task=ticket&id='.$row->id); ?>" class="tooltips" title="#<?php echo $row->id.' :: '.htmlentities(stripslashes($row->summary), ENT_QUOTES); ?>">#<?php echo $row->id.': '.Hubzero_View_Helper_Html::shortenText(stripslashes($row->summary), 35, 0); ?></a>
			<span><span><?php echo $when; ?></span>, <span><?php echo JText::sprintf('MOD_MYTICKETS_COMMENTS',$row->comments); ?></span></span>
		</li>
<?php
	}
?>
	</ul>
<?php } ?>
<h4><?php echo JText::_('MOD_MYTICKETS_CONTRIBUTIONS'); ?></h4>
<?php
$rows3 = $this->rows3;
if (count($rows3) <= 0) {
?>
	<p><?php echo JText::_('MOD_MYTICKETS_NO_TICKETS'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
	foreach ($rows3 as $row)
	{
		$when = $this->timeAgo($row->created);
?>
		<li class="support-ticket <?php echo $row->severity; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_support&task=ticket&id='.$row->id); ?>" class="tooltips" title="#<?php echo $row->id.' :: '.htmlentities(stripslashes($row->summary), ENT_QUOTES); ?>">#<?php echo $row->id.': '.Hubzero_View_Helper_Html::shortenText(stripslashes($row->summary), 35, 0); ?></a>
			<span><span><?php echo $when; ?></span>, <span><?php echo JText::sprintf('MOD_MYTICKETS_COMMENTS',$row->comments); ?></span></span>
		</li>
<?php
	}
?>
	</ul>
<?php } ?>
	
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_support&task=tickets'); ?>"><?php echo JText::_('MOD_MYTICKETS_ALL_TICKETS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_feedback&task=report_problems'); ?>"><?php echo JText::_('MOD_MYTICKETS_NEW_TICKET'); ?></a></li>
	</ul>
</div>
