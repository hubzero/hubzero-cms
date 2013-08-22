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

// Use id or alias in urls?
$goto  = 'alias=' . $this->project->alias;
?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_LEAVE_PROJECT'); ?></h3>
<form id="hubForm-ajax" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="quit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="team" />
		<input type="hidden" name="confirm" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<?php if($this->onlymanager) { ?>
			<p class="warning"><?php echo JText::_('COM_PROJECTS_TEAM_LEAVE_PROJECT_ONLY_MANAGER'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task=edit').'/?edit=team'; ?>"><?php echo JText::_('COM_PROJECTS_TEAM'); ?></a>.</p>
		<?php } else if($this->group) { 
		$group = Hubzero_Group::getInstance( $this->group );
		?>
			<p class="warning"><?php echo JText::_('COM_PROJECTS_TEAM_LEAVE_GROUP_MEMBER'); ?> <a href="<?php echo JRoute::_('index.php?option=com_groups'.a.'gid='.$group->get('gidNumber')); ?>"><?php echo $group->get('description'); ?></a> <?php echo JText::_('COM_PROJECTS_TEAM_LEAVE_GROUP_MEMBER_QUIT'); ?></p>
		<?php } else { ?>
			<p class="warning"><?php echo JText::_('COM_PROJECTS_TEAM_LEAVE_PROJECT_NOTE'); ?></p>
			<h4><?php echo JText::_('COM_PROJECTS_TEAM_LEAVE_PROJECT'); ?></h4>
			<p>
				<span><input type="submit" class="confirm" value="<?php echo JText::_('COM_PROJECTS_LEAVE_PROJECT'); ?>" /></span>
				<span><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto); ?>" class="confirm"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></span>
			</p>
		<?php } ?>
	</fieldset>
</form>
</div>