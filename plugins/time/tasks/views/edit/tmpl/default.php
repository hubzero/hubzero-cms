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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div id="dialog-confirm"></div>

<div id="plg_time_tasks">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="back icon-back btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks'.$this->start); ?>">
					<?php echo JText::_('PLG_TIME_TASKS_ALL_TASKS'); ?>
				</a>
			</li>
			<li class="last">
				<a class="delete icon-delete btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=delete&id='.$this->row->id); ?>">
					<?php echo JText::_('PLG_TIME_TASKS_DELETE'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="outer-container">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=save'); ?>" method="post">
			<div class="title"><?php echo JText::_('PLG_TIME_TASKS_'.strtoupper($this->action).'_CAPTION'); ?></div>
			<div class="grouping" id="name-group">
				<label for="name"><?php echo JText::_('PLG_TIME_TASKS_NAME'); ?>:</label>
				<input type="text" name="task[name]" id="name" value="<?php echo htmlentities(stripslashes($this->row->name), ENT_QUOTES); ?>" size="50" />
			</div>

			<div class="grouping" id="active-group">
				<label><?php echo JText::_('PLG_TIME_TASKS_ACTIVE'); ?>:</label>
				<input type="radio" name="task[active]" id="active_yes" value="1" <?php if($this->row->active == 1 || $this->row->active === NULL){ echo "checked"; } ?> />Yes
				<input type="radio" name="task[active]" id="active_no" value="0" <?php if($this->row->active !== NULL && $this->row->active == 0){ echo "checked"; } ?> />No
			</div>

			<div class="grouping" id="hub-group">
				<label for="hub"><?php echo JText::_('PLG_TIME_TASKS_HUB_NAME'); ?>:</label>
				<?php echo $this->hlist; ?>
			</div>

			<div class="grouping" id="startdate-group">
				<label for="startdate"><?php echo JText::_('PLG_TIME_TASKS_START_DATE'); ?>:</label>
				<input type="text" name="task[start_date]" id="startdate" class="hadDatepicker" value="<?php echo htmlentities(stripslashes($this->row->start_date), ENT_QUOTES); ?>" size="10" />
			</div>

			<div class="grouping" id="enddate-group">
				<label for="enddate"><?php echo JText::_('PLG_TIME_TASKS_END_DATE'); ?>:</label>
				<input type="text" name="task[end_date]" id="enddate" class="hadDatepicker" value="<?php echo htmlentities(stripslashes($this->row->end_date), ENT_QUOTES); ?>" size="10" />
			</div>

			<div class="grouping" id="priority-group">
				<label for="priority"><?php echo JText::_('PLG_TIME_TASKS_PRIORITY'); ?>:</label>
				<?php echo $this->priority_list; ?>
			</div>

			<div class="grouping" id="assignee-group">
				<label for="assignee"><?php echo JText::_('PLG_TIME_TASKS_ASSIGNEE'); ?>:</label>
				<?php echo $this->alist; ?>
			</div>

			<div class="grouping" id="liaison-group">
				<label for="liaison"><?php echo JText::_('PLG_TIME_TASKS_LIAISON'); ?>:</label>
				<?php echo $this->llist; ?>
			</div>

			<div class="grouping" id="description-group">
				<label for="description"><?php echo JText::_('PLG_TIME_TASKS_DESCRIPTION'); ?>:</label>
				<textarea name="task[description]" id="description" rows="6" cols="50"><?php echo htmlentities(stripslashes($this->row->description), ENT_QUOTES); ?></textarea>
			</div>

			<input type="hidden" name="task[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="active" value="tasks" />
			<input type="hidden" name="action" value="save" />
	</div><!-- //container -->
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_TIME_TASKS_SUBMIT'); ?>" />
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks'.$this->start); ?>"><span class="cancel-button"><?php echo JText::_('PLG_TIME_TASKS_CANCEL'); ?></span></a>
			</p>
		</form>
</div>