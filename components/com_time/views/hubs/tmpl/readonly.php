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

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('hubs')
     ->js('hubs')
     ->js();

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>

<div id="dialog-confirm"></div>
<div id="dialog-message"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_hubs">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li>
					<a class="icon-reply btn" href="<?php echo JRoute::_($base . $this->start); ?>">
						<?php echo JText::_('COM_TIME_HUBS_ALL_HUBS'); ?>
					</a>
				</li>
				<li>
					<a class="icon-edit btn" href="<?php echo JRoute::_($base . '&task=edit&id=' . $this->row->id); ?>">
						<?php echo JText::_('COM_TIME_HUBS_EDIT'); ?>
					</a>
				</li>
				<li class="last">
					<a class="delete icon-delete btn" href="<?php echo JRoute::_($base . '&task=delete&id=' . $this->row->id); ?>">
						<?php echo JText::_('COM_TIME_HUBS_DELETE'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="container readonly">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="grid">
				<div class="col span-third">
					<h3 class="headings"><?php echo JText::_('COM_TIME_HUBS_DETAILS'); ?></h3>
					<div class="grouping name-grouping">
						<label for="name"><?php echo JText::_('COM_TIME_HUBS_NAME'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->name)); ?>
					</div>

					<div class="grouping liaison-grouping">
						<label for="liaison"><?php echo JText::_('COM_TIME_HUBS_LIAISON'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->liaison)); ?>
					</div>

					<div class="grouping anniversary-grouping">
						<label for="anniversary_date"><?php echo JText::_('COM_TIME_HUBS_ANNIVERSARY_DATE'); ?>:</label>
						<?php echo ($this->row->anniversary_date != '0000-00-00') ? JHTML::_('date', $this->row->anniversary_date, 'm/d/y', null) : ''; ?>
					</div>

					<div class="grouping support-grouping">
						<label for="support_level"><?php echo JText::_('COM_TIME_HUBS_SUPPORT_LEVEL'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->support_level)); ?>
					</div>

					<div class="grouping tasks-grouping">
						<label for="active_tasks"><?php echo JText::_('COM_TIME_HUBS_ACTIVE_TASKS'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->activeTasks)); ?>
					</div>

					<div class="grouping hours-grouping">
						<label for="total_hours"><?php echo JText::_('COM_TIME_HUBS_TOTAL_HOURS'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->totalHours)); ?>
					</div>
				</div>

				<div class="col span-third">
					<h3 class="headings"><?php echo JText::_('COM_TIME_HUBS_CONTACTS'); ?></h3>

					<?php $i = 0; ?>
					<?php if (count($this->contacts) > 0) : ?>
						<?php foreach ($this->contacts as $contact) : ?>
							<div class="contact-entry">
								<div class="contact-name"><?php  echo $this->escape(stripslashes($contact->name));  ?></div>
								<div class="contact-phone"><?php echo $this->escape(stripslashes($contact->phone)); ?></div>
								<div class="contact-email"><?php echo $this->escape(stripslashes($contact->email)); ?></div>
								<div class="contact-role"><?php  echo $this->escape(stripslashes($contact->role));  ?></div>
							</div>
							<?php $i++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<p><?php echo JText::_('COM_TIME_HUBS_NO_CONTACTS'); ?></p>
					<?php endif; ?>
				</div>

				<div class="col span-third omega">
					<h3 class="headings"><?php echo JText::_('COM_TIME_HUBS_NOTES'); ?></h3>
					<?php if (!empty($this->row->notes)) : ?>
						<div class="hub-notes">
							<div class="inner">
								<?php echo $this->row->notes; ?>
							</div>
						</div>
					<?php else : ?>
						<p>
							<?php echo JText::_('COM_TIME_HUBS_NO_NOTES'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div><!-- / .grid -->
		</div><!-- / .container -->
	</section>
</div>