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

<div id="plg_time_reports">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach
	} // close if count ?>

	<div class="grid">
		<div class="col span-half">
			<div class="new_report_box">
				<h3><?php echo JText::_('PLG_TIME_REPORTS_CREATE_NEW'); ?></h3>
				<h4><?php echo JText::_('PLG_TIME_REPORTS_CHOOSE_REPORT_TYPE'); ?></h4>
				<ul>
					<li>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active='.$this->active.'&action=createbill'); ?>">Bill</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="col span-half omega">
			<div class="view_report_box">
				<h3><?php echo JText::_('PLG_TIME_REPORTS_VIEW_SAVED'); ?></h3>
				<h4><?php echo JText::_('PLG_TIME_REPORTS_SELECT_REPORT'); ?></h4>
				<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active='.$this->active.'&action=viewbill'); ?>">
					<?php echo $this->rlist; ?>
					<input type="submit" value="<?php echo JText::_('PLG_TIME_REPORTS_GO'); ?>" />
				</form>
			</div>
		</div>
	</div>

</div>