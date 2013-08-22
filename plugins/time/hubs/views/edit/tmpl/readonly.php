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
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M j, Y';
	$tz = false;
}

// if count of contacts is greater than zero, set ccount true
$ccount = (count($this->contacts) > 0) ? true : false;
?>

<div id="dialog-confirm"></div>
<div id="dialog-message"></div>

<div id="plg_time_hubs">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="back icon-back btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs'.$this->start); ?>">
					<?php echo JText::_('PLG_TIME_HUBS_ALL_HUBS'); ?>
				</a>
			</li>
			<li>
				<a class="edit icon-edit btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=edit&id='.$this->row->id); ?>">
					<?php echo JText::_('PLG_TIME_HUBS_EDIT'); ?>
				</a>
			</li>
			<li class="last">
				<a class="delete icon-delete btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=delete&id='.$this->row->id); ?>">
					<?php echo JText::_('PLG_TIME_HUBS_DELETE'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="outer-container readonly">
		<div class="three columns first">
			<h3 class="headings"><?php echo JText::_('PLG_TIME_HUBS_DETAILS'); ?></h3>
			<div class="grouping" id="name-grouping">
				<label for="name"><?php echo JText::_('PLG_TIME_HUBS_NAME'); ?>:</label>
				<?php echo htmlentities(stripslashes($this->row->name), ENT_QUOTES); ?>
			</div>

			<div class="grouping" id="liaison-grouping">
				<label for="liaison"><?php echo JText::_('PLG_TIME_HUBS_LIAISON'); ?>:</label>
				<?php echo htmlentities(stripslashes($this->row->liaison), ENT_QUOTES); ?>
			</div>

			<div class="grouping" id="anniversary-grouping">
				<label for="anniversary_date"><?php echo JText::_('PLG_TIME_HUBS_ANNIVERSARY_DATE'); ?>:</label>
				<?php echo ($this->row->anniversary_date != '0000-00-00') ? JHTML::_('date', $this->row->anniversary_date, $dateFormat, $tz) : ''; ?>
			</div>

			<div class="grouping" id="support-grouping">
				<label for="support_level"><?php echo JText::_('PLG_TIME_HUBS_SUPPORT_LEVEL'); ?>:</label>
				<?php echo htmlentities(stripslashes($this->row->support_level), ENT_QUOTES); ?>
			</div>

			<div class="grouping" id="tasks-grouping">
				<label for="active_tasks"><?php echo JText::_('PLG_TIME_HUBS_ACTIVE_TASKS'); ?>:</label>
				<?php echo htmlentities(stripslashes($this->activeTasks), ENT_QUOTES); ?>
			</div>

			<div class="grouping" id="hours-grouping">
				<label for="total_hours"><?php echo JText::_('PLG_TIME_HUBS_TOTAL_HOURS'); ?>:</label>
				<?php echo htmlentities(stripslashes($this->totalHours), ENT_QUOTES); ?>
			</div>
		</div>

		<div class="three columns second">
			<h3 class="headings"><?php echo JText::_('PLG_TIME_HUBS_CONTACTS'); ?></h3>

			<?php $i = 0;
				if($ccount)
				{
					foreach($this->contacts as $contact)
					{ ?>
						<div class="contact-entry">
							<div class="contact-name"><?php echo htmlentities(stripslashes($contact->name), ENT_QUOTES); ?></div>
							<div class="contact-phone"><?php echo htmlentities(stripslashes($contact->phone), ENT_QUOTES); ?></div>
							<div class="contact-email"><?php echo htmlentities(stripslashes($contact->email), ENT_QUOTES); ?></div>
							<div class="contact-role"><?php echo htmlentities(stripslashes($contact->role), ENT_QUOTES); ?></div>
						</div>
						<?php $i++;
					} // close foreach contacts
				} else { // close if ccount ?>
					<p><?php echo JText::_('PLG_TIME_HUBS_NO_CONTACTS'); ?></p>
				<?php } // close else ccount ?>
		</div>

		<div class="three columns third">
			<h3 class="headings"><?php echo JText::_('PLG_TIME_HUBS_NOTES'); ?></h3>
			<?php if(!empty($this->row->notes)) {
				echo '<div class="hub-notes"><div class="inner">';
				echo $this->row->notes;
				echo '</div></div>';
			} else {
				echo '<p>' . JText::_('PLG_TIME_HUBS_NO_NOTES') . '</p>';
			}?>
		</div>
	</div><!-- //container -->
</div>
