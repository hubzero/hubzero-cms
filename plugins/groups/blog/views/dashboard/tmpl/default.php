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
<table class="activity" summary="<?php echo JText::_('PLG_GROUPS_BLOG_ENTRIES_TABLE_SUMMARY'); ?>">
	<tbody>
<?php 
if ($this->entries) {
	foreach ($this->entries as $entry) 
	{
?>
		<tr>
			<th scope="row"><?php echo $area; ?></th>
			<td class="author"><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$entry->created_by); ?>"><?php echo stripslashes($name); ?></a></td>
			<td class="action"><?php echo stripslashes($entry->title); ?></td>
			<td class="date"><?php echo JHTML::_('date', $entry->publish_up, '%b. %d, %Y @%I:%M %p'); ?></td>
		</tr>
<?php
	}
} else {
	// Do nothing if there are no events to display
?>
		<tr>
			<td><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></td>
		</tr>
<?php 
}
?>
	</tbody>
</table>