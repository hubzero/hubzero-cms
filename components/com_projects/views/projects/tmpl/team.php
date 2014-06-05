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

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = ProjectsHtml::cleanText($this->project->about);

$goto  = 'alias=' . $this->project->alias;

$title = $this->project->title ? JText::_('COM_PROJECTS_NEW_PROJECT').': '.$this->project->title : $this->title;
?>
<header id="content-header">
	<h2><?php echo $title; ?> <?php if ($this->gid && is_object($this->group)) { ?> <?php echo JText::_('COM_PROJECTS_FOR').' '.ucfirst(JText::_('COM_PROJECTS_GROUP')); ?> <a href="<?php echo JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>"><?php echo \Hubzero\Utility\String::truncate($this->group->get('description'), 50); ?></a><?php } ?></h2>
</header><!-- / #content-header -->

<section class="main section" id="setup">
	<ul id="status-bar" class="moving">
		<li <?php if ($this->stage == 0) { echo 'class="active"'; } ?>><?php if ($this->project->setup_stage > 0 && $this->stage != 0) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=setup&' . $goto) . '/?step=0'; ?>"<?php if ($this->project->setup_stage >= 1) { echo ' class="c_passed"'; } ?>><?php } ?><?php echo JText::_('COM_PROJECTS_DESCRIBE_PROJECT'); ?><?php if ($this->project->setup_stage > 0 && $this->stage != 0) { ?></a><?php } ?></li>
		<li <?php if ($this->stage == 1) { echo 'class="active"'; } ?>><?php if ($this->project->setup_stage >= 1 && $this->stage != 1) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=setup&' . $goto) . '/?step=1'; ?>"><?php } ?><?php echo JText::_('COM_PROJECTS_ADD_TEAM'); ?><?php if ($this->project->setup_stage >= 1 && $this->stage != 1) { ?></a><?php } ?></li>
		<li><?php echo JText::_('COM_PROJECTS_READY_TO_GO'); ?></li>
	</ul>

	<div class="clear"></div>

	<div class="info_blurb">
		<div class="pthumb"><img src="<?php echo $this->thumb_src; ?>" alt="" /></div>
		<div class="pinfo">
			<p class="info_title"><span class="block italic"><?php echo $this->typetitle.' '.strtolower(JText::_('COM_PROJECTS_PROJECT')); ?>:</span> <?php echo $this->project->title; ?> (<span class="aliasname"><?php echo $this->project->alias; ?></span>)</p>
			<p class="actionlink"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=setup&' . $goto) . '/?step=0'; ?>">&laquo; <?php echo JText::_('COM_PROJECTS_CHANGE_THIS_INFO'); ?></a></p>
		</div>
		<div class="clear"></div>
	</div>

	<div class="status-msg">
		<?php
		// Display error or success message
		if ($this->getError())
		{
			echo '<p class="witherror">' . $this->getError().'</p>';
		}
		elseif ($this->msg)
		{
			echo '<p>' . $this->msg . '</p>';
		}
		?>
	</div>

	<form id="hubForm" method="post" action="index.php">
		<div class="explaination">
			<h4><?php echo JText::_('COM_PROJECTS_HOWTO_TITLE_ROLES'); ?></h4>
			<p><span class="italic prominent"><?php echo ucfirst(JText::_('COM_PROJECTS_LABEL_COLLABORATORS')); ?></span> <?php echo JText::_('COM_PROJECTS_CAN'); ?>:</p>
			<ul>
				<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE'); ?></li>
			</ul>
			<p><span class="italic prominent"><?php echo ucfirst(JText::_('COM_PROJECTS_LABEL_OWNERS')); ?></span> <?php echo JText::_('COM_PROJECTS_CAN'); ?>:</p>
			<ul>
				<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE'); ?></li>
				<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO'); ?></li>
				<li><strong><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE'); ?></strong></li>
			</ul>
			<?php if ($this->gid) { ?>
				<h4><?php echo JText::_('COM_PROJECTS_HOWTO_GROUP_PROJECT'); ?></h4>
				<p><?php echo JText::_('COM_PROJECTS_HOWTO_GROUP_EXPLAIN'); ?></p>
			<?php } ?>
		</div>

		<fieldset class="wider">
			<input type="hidden" name="task" value="setup" />
			<input type="hidden" name="step" value="1" />
			<input type="hidden" name="save_stage" value="2" />
			<input type="hidden" id="option" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" id="pid" name="id" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" id="tempid" name="tempid" value="<?php echo $this->tempid; ?>" />
			<input type="hidden" id="gid" name="gid" value="<?php echo $this->gid; ?>" />
			<h2><?php echo JText::_('COM_PROJECTS_ADD_MEMBERS_TO_PROJECT'); ?></h2>

			<div id="cbody">
				<?php echo $this->content; ?>
			</div>
			<p class="submitarea"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn" /></p>
		</fieldset>
	</form>
	<div class="clear"></div>
</section>
