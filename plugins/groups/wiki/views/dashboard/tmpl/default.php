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

if ($this->rows) {
?>
	<table class="activity" id="wiki-list" summary="<?php echo JText::_('PLG_GROUPS_WIKI_DASHBOARD_SUMMARY'); ?>">
		<tbody>
<?php
	$cls = 'even';
	foreach ($this->rows as $row)
	{
		$name = JText::_('WIKI_AUTHOR_UNKNOWN');
		$juser =& JUser::getInstance( $row->created_by );
		if (is_object($juser) && $juser->get('name')) {
			$name = $juser->get('name');
		}
		
		if ($row->version > 1) {
			$t = JText::_('WIKI_EDITED');
			$c = 'wiki-edited';
		} else {
			$t = JText::_('WIKI_CREATED');
			$c = 'wiki-created';
		}
		
		$cls = ($cls == 'even') ? 'odd' : 'even';
?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><span class="<?php echo $c; ?>"><?php echo $t; ?></span></th>
				<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&pagename='.$row->pagename.'&scope='.$row->scope); ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td class="author"><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo $name; ?></a></td>
				<td class="date"><?php echo JHTML::_('date', $row->created, "%d %b. %Y"); ?></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo JText::_('PLG_GROUPS_WIKI_NO_RESULTS_FOUND'); ?></p>
<?php } ?>