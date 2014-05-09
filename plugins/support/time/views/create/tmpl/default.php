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

$this->css()
     ->js();
?>

<fieldset>
	<legend><?php echo JText::_('PLG_SUPPORT_TIME'); ?></legend>

	<div id="plg_time_records">
		<div class="grid">
			<div class="col span6">
				<div id="time-group">
					<span><?php echo JText::_('PLG_SUPPORT_TIME_TIME'); ?>:</span>
					<?php echo $this->htimelist; ?>
					<?php echo $this->mtimelist; ?>
				</div>
			</div>
			<div class="col span6 omega">
			<label for="date">
				<?php echo JText::_('PLG_SUPPORT_TIME_DATE'); ?>:
				<input type="text" name="record[date]" id="datepicker" class="hadDatepicker" value="<?php echo $this->escape(stripslashes($this->row->date)); ?>" size="10" />
			</label>
		</div>
		</div>
		<div class="clear"></div>

		<div class="grouping">
			<label for="hub"><?php echo JText::_('PLG_SUPPORT_TIME_HUB'); ?>:
			<?php echo $this->hubslist; ?>
			</label>

			<label for="task"><?php echo JText::_('PLG_SUPPORT_TIME_TASK'); ?>:
			<?php echo $this->tasklist; ?>
			</label>
		</div>
		<div class="clear"></div>

		<input type="hidden" name="record[id]" value="0" />
	</div>
</fieldset>