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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$role  = JText::_('COM_PROJECTS_PROJECT') . ' <span>';
$role .= $this->project->role == 1 ? JText::_('COM_PROJECTS_LABEL_OWNER') : JText::_('COM_PROJECTS_LABEL_COLLABORATOR');
$role .= '</span>';

?>
<ul id="member_options">
	<li><?php echo ucfirst($role); ?>
		<div id="options-dock">
			<div><p><?php echo JText::_('COM_PROJECTS_JOINED') . ' ' . JHTML::_('date', $this->project->since, 'M d, Y'); ?></p>
				<ul>
		<?php if ($this->project->role == 1) { ?>
					<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&task=edit'); ?>"><?php echo JText::_('COM_PROJECTS_EDIT_PROJECT'); ?></a></li>
					<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&task=edit&active=team'); ?>"><?php echo JText::_('COM_PROJECTS_INVITE_PEOPLE'); ?></a></li>
		<?php } ?>
		<?php if (!$this->project->private) { ?>
					<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&preview=1'); ?>"><?php echo JText::_('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a></li>
		<?php } ?>
		<?php if (isset($this->project->counts['team']) && $this->project->counts['team'] > 1) { ?>
					<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=team&action=quit'); ?>"><?php echo JText::_('COM_PROJECTS_LEAVE_PROJECT'); ?></a></li>
		<?php } ?>
				</ul>
			</div>
		</div>
	</li>
</ul>