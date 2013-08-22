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

$dateFormat = '%b %e, %Y';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M j, Y';
	$tz = null;
}
?>

<div id="dialog-confirm"></div>

<div id="plg_time_records">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<?php if($this->row->billed == 0 && ($this->juser->get('id') == $this->row->user_id || in_array($this->row->user_id, $this->subordinates))) { ?>
				<li>
					<a class="back icon-back btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'.$this->start); ?>">
						<?php echo JText::_('PLG_TIME_RECORDS_ALL_RECORDS'); ?>
					</a>
				</li>
				<li>
					<a class="edit icon-edit btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&action=edit&id='.$this->row->id); ?>">
						<?php echo JText::_('PLG_TIME_RECORDS_EDIT'); ?>
					</a>
				</li>
				<li class="last">
					<a class="delete icon-delete btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&action=delete&id='.$this->row->id); ?>">
						<?php echo JText::_('PLG_TIME_RECORDS_DELETE'); ?>
					</a>
				</li>
			<?php } else { ?>
				<li class="last">
					<a class="back icon-back btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'.$this->start); ?>">
						<?php echo JText::_('PLG_TIME_RECORDS_ALL_RECORDS'); ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
	<?php if($this->row->billed == 1) : ?>
		<p class="info"><?php echo JText::_('PLG_TIME_RECORDS_RECORD_BILLED'); ?></p>
	<?php endif; ?>
	<div class="readonly two columns first">
		<h3 class="headings"><?php echo JText::_('PLG_TIME_RECORDS_DETAILS'); ?></h3>
		<div class="grouping" id="uname-group">
			<label for="uname"><?php echo JText::_('PLG_TIME_RECORDS_USER'); ?>:</label>
			<?php echo htmlentities(stripslashes($this->row->uname), ENT_QUOTES); ?>
		</div>

		<div class="grouping" id="hub-group">
			<label for="hub"><?php echo JText::_('PLG_TIME_RECORDS_HUB'); ?>:</label>
			<?php echo htmlentities(stripslashes($this->row->hname), ENT_QUOTES); ?>
		</div>

		<div class="grouping" id="task-group">
			<label for="task"><?php echo JText::_('PLG_TIME_RECORDS_TASK'); ?>:</label>
			<?php echo htmlentities(stripslashes($this->row->pname), ENT_QUOTES); ?>
		</div>

		<div class="grouping" id="time-group">
			<label for="time"><?php echo JText::_('PLG_TIME_RECORDS_TIME'); ?>:</label>
			<?php echo htmlentities(stripslashes($this->row->time), ENT_QUOTES); ?> hour(s)
		</div>

		<div class="grouping" id="date-group">
			<label for="date"><?php echo JText::_('PLG_TIME_RECORDS_DATE'); ?>:</label>
			<?php echo ($this->row->date != '0000-00-00 00:00:00') ? JHTML::_('date', $this->row->date, $dateFormat, $tz) : ''; ?>
		</div>
	</div>

	<div class="readonly two columns second">
		<h3 class="headings"><?php echo JText::_('PLG_TIME_RECORDS_DESCRIPTION'); ?></h3>
		<?php if(!empty($this->row->description)) {
			echo '<div class="hub-notes"><div class="inner">';
			echo '<p>' . $this->row->description . '</p>';
			echo '</div></div>';
		} else {
			echo '<p>' . JText::_('PLG_TIME_RECORDS_NO_DESCRIPTION') . '</p>';
		}?>
		</div>
	</div>
</div>
