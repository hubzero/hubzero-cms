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
<div<?php echo ($this->cssId) ? ' id="' . $this->cssId . '"' : ''; echo ($this->cssClass) ? ' class="' . $this->cssClass . '"' : ''; ?>>
<?php if ($this->rows) { ?>
	<ul class="articles">
<?php
	$juser =& JFactory::getUser();
	foreach ($this->rows as $row)
	{
		if ($row->access <= $juser->get('aid')) {
			$link = 'index.php?option=com_kb&amp;section='.$row->section;
			$link .= ($row->category) ? '&amp;category='.$row->category : '';
			$link .= ($row->alias) ? '&amp;alias='. $row->alias : '&amp;alias='. $row->id;

			$link_on = JRoute::_($link);
		} else {
			$link_on = JRoute::_('index.php?option=com_register&task=register');
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
