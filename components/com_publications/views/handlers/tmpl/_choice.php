<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
// no direct access
defined('_JEXEC') or die('Restricted access');

$class = $this->item->assigned && $this->item->active ? ' assigned' : ' unassigned';
?>

<div class="handlertype-<?php echo $this->handler->get('_name') . $class; ?>">
	<h3><?php echo $this->configs->label; ?></h3>
	<p class="manage-handler">
		<a href="<?php echo JRoute::_('index.php?option=com_projects&alias='
				. $this->publication->project_alias . '&active=publications&pid=' . $this->publication->id) . '?vid=' . $this->publication->version_id . '&amp;action=handler&amp;h=' . $this->handler->get('_name') . '&amp;p=' . $this->props; ?>" class="showinbox box-expanded"><?php echo ($this->item->assigned && $this->item->active) ? JText::_('COM_PUBLICATIONS_HANDLER_VIEW_MANAGE') : JText::_('COM_PUBLICATIONS_HANDLER_ACTIVATE'); ?></a>
	</p>
</div>