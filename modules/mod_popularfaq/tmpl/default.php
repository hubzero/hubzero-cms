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

$rows = $modpopularfaq->rows;
?>
<div id="<?php echo $modpopularfaq->moduleid; ?>">
<?php if ($rows) { ?>
	<ul class="articles">
<?php
	foreach ($rows as $row) 
	{
		if ($row->access <= $juser->get('aid')) {
			$link = 'index.php?option=com_kb&amp;section='.$row->section;
			$link .= ($row->category) ? '&amp;category='.$row->category : '';
			$link .= ($row->alias) ? '&amp;alias='. $row->alias : '&amp;alias='. $row->id;
			
			$link_on = JRoute::_($link);
		} else {
			$link_on = JRoute::_('index.php?option=com_hub&task=register');
		}
?>
		<li><a href="<?php echo $link_on; ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php
	}
?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('MOD_POPULARFAQ_NO_ARTICLES_FOUND'); ?></p>
<?php } ?>
</div>