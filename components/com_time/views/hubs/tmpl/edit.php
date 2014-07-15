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

\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('hubs')
     ->js('hubs')
     ->js('time');

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

?>

<div id="dialog-confirm"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_hubs">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-reply btn" href="<?php echo JRoute::_($base . $this->start); ?>">
						<?php echo JText::_('COM_TIME_HUBS_ALL_HUBS'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
					<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<form action="<?php echo JRoute::_($base . '&task=save'); ?>" method="post">
				<div class="title"><?php echo JText::_('COM_TIME_HUBS_' . strtoupper($this->task)) . ': ' . $this->row->name; ?></div>
				<div class="grouping" id="name-group">
					<label for="name"><?php echo JText::_('COM_TIME_HUBS_NAME'); ?>:</label>
					<input type="text" name="hubs[name]" id="name" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" size="50" />
				</div>

				<label for="contact"><?php echo JText::_('COM_TIME_HUBS_CONTACTS'); ?>:</label>
				<?php $i = 0; ?>
				<?php if (count($this->contacts) > 0) : ?>
					<?php foreach ($this->contacts as $contact) : ?>
						<div class="grouping contact-grouping grid" id="contact-<?php echo $contact->id; ?>-group">
							<div class="col span4">
								<input type="text" name="contacts[<?php echo $contact->id; ?>][name]" id="" value="<?php echo $this->escape(stripslashes($contact->name)); ?>" />
							</div>
							<div class="col span2">
								<input type="text" name="contacts[<?php echo $contact->id; ?>][phone]" id="" value="<?php echo $this->escape(stripslashes($contact->phone)); ?>" />
							</div>
							<div class="col span2">
								<input type="text" name="contacts[<?php echo $contact->id; ?>][email]" id="" value="<?php echo $this->escape(stripslashes($contact->email)); ?>" />
							</div>
							<div class="col span2">
								<input type="text" name="contacts[<?php echo $contact->id; ?>][role]" id="" value="<?php echo $this->escape(stripslashes($contact->role)); ?>" />
								<input type="hidden" name="contacts[<?php echo $contact->id; ?>][id]" value="<?php echo $contact->id; ?>" />
							</div>
							<div class="col span2 omega">
								<a href="<?php echo JRoute::_($base . '&task=deletecontact&id=' . $contact->id); ?>" class="btn btn-danger icon-delete delete_contact" title="Delete contact">Delete</a>
							</div>
						</div>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php endif; ?>

				<div class="grouping grid" id="new-contact-group">
					<div class="col span4">
						<input type="text" name="contacts[new][name]" id="new_name" placeholder="name" class="new_contact" />
					</div>
					<div class="col span2">
						<input type="text" name="contacts[new][phone]" id="new_phone" placeholder="phone" class="new_contact" />
					</div>
					<div class="col span2">
						<input type="text" name="contacts[new][email]" id="new_email" placeholder="email" class="new_contact" />
					</div>
					<div class="col span2">
						<input type="text" name="contacts[new][role]" id="new_role" placeholder="role" class="new_contact" />
					</div>
					<div class="col span2 omega">
						<a href="#" id="save_new_contact" class="btn btn-success icon-save save_contact" title="Save contact">Save</a>
					</div>
				</div>

				<div class="grouping" id="liaison-group">
					<label for="liaison"><?php echo JText::_('COM_TIME_HUBS_LIAISON'); ?>:</label>
					<input type="text" name="hubs[liaison]" id="liaison" value="<?php echo $this->escape(stripslashes($this->row->liaison)); ?>" size="50" />
				</div>

				<div class="grouping" id="anniversary-group">
					<label for="anniversary_date"><?php echo JText::_('COM_TIME_HUBS_ANNIVERSARY_DATE'); ?>:</label>
					<input class="hadDatepicker" type="text" name="hubs[anniversary_date]" id="anniversary_date" value="<?php echo $this->escape(stripslashes($this->row->anniversary_date)); ?>" size="50" />
				</div>

				<div class="grouping" id="support-group">
					<label for="support_level"><?php echo JText::_('COM_TIME_HUBS_SUPPORT_LEVEL'); ?>:</label>
					<?php echo $this->slist; ?>
				</div>

				<div class="grouping" id="notes-group">
					<label for="notes"><?php echo JText::_('COM_TIME_HUBS_NOTES'); ?>:</label>
					<?php echo \JFactory::getEditor()->display('hubs[notes]', $this->escape($this->row->notes), '', '', 35, 6, false, 'notes', null, null, array('class' => 'minimal no-footer')); ?>
				</div>

				<input type="hidden" name="hubs[id]" value="<?php echo $this->row->id; ?>" id="hub_id" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="active" value="hubs" />
				<input type="hidden" name="action" value="save" />

				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_TIME_HUBS_SUBMIT'); ?>" />
					<a href="<?php echo JRoute::_($base . $this->start); ?>">
						<button class="btn btn-secondary" type="button"><?php echo JText::_('COM_TIME_HUBS_CANCEL'); ?></button>
					</a>
				</p>
			</form>
		</div>
	</section>
</div>