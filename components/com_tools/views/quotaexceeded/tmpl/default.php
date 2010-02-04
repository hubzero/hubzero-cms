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

$juser =& JFactory::getUser();
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('COM_TOOLS_QUOTAEXCEEDED'); ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<p class="warning"><?php echo JText::_('COM_TOOLS_ERROR_QUOTAEXCEEDED'); ?></p>

	<table class="sessions" summary="<?php echo JText::_('COM_TOOLS_SESSIONS_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th>Session</th>
<?php if ($this->authorized === 'admin') { ?>
				<th>Owner</th>
<?php } ?>
				<th>Started</th>
				<th>Last accessed</th>
				<th>Option</th>
			</tr>
		</thead>
		<tbody>
<?php
if ($this->sessions) {
	$cls = 'even';
	foreach ($this->sessions as $session)
	{
		$cls = ($cls == 'odd') ? 'even' : 'odd';
?>	
			<tr class="<?php echo $cls; ?>">
				<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=view&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_RESUME_TITLE'); ?>"><?php echo $session->sessname; ?></a></td>
<?php if ($this->authorized === 'admin') { ?>
				<td><?php echo $session->username; ?></td>
<?php } ?>
				<td><?php echo $session->start; ?></td>
				<td><?php echo $session->accesstime; ?></td>
<?php if ($juser->get('username') == $session->username || $this->authorized === 'admin') { ?>
				<td><a class="close" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=stop&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_TERMINATE_TITLE'); ?>"><?php echo JText::_('COM_TOOLS_TERMINATE'); ?></a></td>
<?php } else { ?>
				<td><a class="disconnect" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=unshare&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_DISCONNECT_TITLE'); ?>"><?php echo JText::_('COM_TOOLS_DISCONNECT'); ?></a> <br /><?php echo JText::_('MY_SESSIONS_OWNER').': '.$session->username; ?></td>
<?php } ?>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
</div><!-- / .section -->