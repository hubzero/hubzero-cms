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

<div id="plg_time_records">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="back icon-back btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'.$this->start); ?>">
					<?php echo JText::_('PLG_TIME_RECORDS_ALL_RECORDS'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="outer-container">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&action=save'); ?>" method="post">
			<div class="title"><?php echo JText::_('PLG_TIME_RECORDS_'.strtoupper($this->action).'_CAPTION'); ?></div>

			<div class="grouping" id="uname-group">
				<label for="uname"><?php echo JText::_('PLG_TIME_RECORDS_USER'); ?>:</label>
				<?php if (isset($this->subordinates)) : ?>
					<?php echo $this->subordinates; ?>
				<?php else : ?>
					<?php echo htmlentities(stripslashes($this->row->uname), ENT_QUOTES); ?>
					<input type="hidden" name="record[user_id]" value="<?php echo $this->row->user_id; ?>" />
				<?php endif; ?>
			</div>

			<div class="grouping" id="time-group">
				<label for="time" style="float:left;"><?php echo JText::_('PLG_TIME_RECORDS_TIME'); ?>:</label>
				<div style="width: 75px; float:left; margin-right: 5px; margin-left: 5px"><?php echo $this->htimelist; ?></div>
				<div style="width: 75px; float:left;"><?php echo $this->mtimelist; ?></div>
			</div>

			<div class="grouping" id="date-group">
				<label for="date"><?php echo JText::_('PLG_TIME_RECORDS_DATE'); ?>:</label>
				<input type="text" name="record[date]" id="datepicker" class="hadDatepicker" value="<?php echo htmlentities(stripslashes($this->row->date), ENT_QUOTES); ?>" size="10" />
			</div>

			<div class="grouping" id="hub-group">
				<label for="hub"><?php echo JText::_('PLG_TIME_RECORDS_HUB'); ?>:</label>
				<?php echo $this->hubslist; ?>
			</div>

			<div class="grouping" id="task-group">
				<label for="task"><?php echo JText::_('PLG_TIME_RECORDS_TASK'); ?>:</label>
				<?php echo $this->tasklist; ?>
			</div>

			<div class="grouping" id="description-group">
				<label for="description"><?php echo JText::_('PLG_TIME_RECORDS_DESCRIPTION'); ?>:</label>
				<textarea name="record[description]" id="description" rows="6" cols="50"><?php echo htmlentities(stripslashes($this->row->description), ENT_QUOTES); ?></textarea>
			</div>

			<input type="hidden" name="record[id]" value="<?php echo $this->row->id; ?>" />
	</div><!-- //container -->
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_TIME_RECORDS_SUBMIT'); ?>" />
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'.$this->start); ?>"><span class="cancel-button"><?php echo JText::_('PLG_TIME_RECORDS_CANCEL'); ?></span></a>
			</p>
		</form>
</div>