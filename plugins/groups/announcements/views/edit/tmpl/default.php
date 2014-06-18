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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//add styles and scripts
$this->css()
     ->css('jquery.datepicker.css', 'system')
     ->css('jquery.timepicker.css', 'system');

$this->js()
     ->js('jquery.timepicker', 'system');
?>

<ul id="page_options">
	<li>
		<a class="icon-prev back btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->cn . '&active=announcements'); ?>">
			<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_BACK'); ?>
		</a>
	</li>
</ul>

<section class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=announcements'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_HINT'); ?>
		</div><!-- /.aside -->

		<fieldset>
			<legend>
				<?php if ($this->announcement->get('id')) : ?>
						<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_EDIT'); ?>
				<?php else : ?>
						<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_NEW'); ?>
				<?php endif; ?>
			</legend>

			<label for="field_content">
				<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_ANNOUNCEMENT'); ?> <span class="required"><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_REQUIRED'); ?></span>
				<?php
				echo \JFactory::getEditor()->display('fields[content]', $this->escape(stripslashes($this->announcement->get('content'))), '', '', 35, 5, false, 'field_content', null, null, array('class' => 'minimal no-footer'));
				?>
			</label>

			<fieldset>
				<legend><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_WINDOW'); ?> <span class="optional"><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_OPTIONAL'); ?></span></legend>

				<div class="grid">
					<div class="col span-half">
						<label for="field-publish_up" id="priority-publish_up">
							<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_START'); ?>
							<?php
								$publish_up = $this->announcement->get('publish_up');
								if ($publish_up != '' && $publish_up != '0000-00-00 00:00:00')
								{
									$publish_up = JHTML::_('date', $publish_up, 'Y-m-d H:i:s');
									$publish_up = date("m/d/Y @ g:i a", strtotime($publish_up));
								}
							?>
							<input class="datepicker" type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo $this->escape($publish_up); ?>" />
							<span class="hint"><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_HINT'); ?></span>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-publish_down" id="priority-publish_down">
							<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_END'); ?>
							<?php
								$publish_down = $this->announcement->get('publish_down');
								if ($publish_down != '' && $publish_down != '0000-00-00 00:00:00')
								{
									$publish_down = JHTML::_('date', $publish_down, 'Y-m-d H:i:s');
									$publish_down = date("m/d/Y @ g:i a", strtotime($publish_down));
								}
							?>
							<input class="datepicker" type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo $this->escape($publish_down); ?>" />
							<span class="hint"><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_HINT'); ?></span>
						</label>
					</div>
				</div>
			</fieldset>

			<label for="field-email" id="email-label">
				<input class="option" type="checkbox" name="fields[email]" id="field-email" value="1" <?php if ($this->announcement->email == 1) { echo 'checked="checked"'; } ?> />
				<?php if ($this->announcement->sent == 1) : ?>
					<span class="important"><?php echo JText::_('Announcement already emailed, send again?'); ?></span>
				<?php else : ?>
					<?php echo JText::_('Email announcement to group members?'); ?>
				<?php endif; ?>
			</label>

			<label for="field-priority" id="priority-label">
				<input class="option" type="checkbox" name="fields[priority]" id="field-priority" value="1"<?php if ($this->announcement->get('priority')) { echo ' checked="checked"'; } ?> />
				<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_MARK_HIGH_PRIORITY'); ?> <a href="#" class="tooltips" title="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_MARK_HIGH_PRIORITY_TITLE'); ?>">?</a>
			</label>
			<label for="field-sticky" id="sticky-label">
				<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->announcement->get('sticky')) { echo ' checked="checked"'; } ?> />
				<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_MARK_STICKY'); ?> <a href="#" class="tooltips" title="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_MARK_STICKY_TITLE'); ?>">?</a>
			</label>

			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_SAVE'); ?>" />
			</p>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->announcement->get('id')); ?>" />
		<input type="hidden" name="fields[state]" value="1" />
		<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->announcement->get('scope')); ?>" />
		<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->announcement->get('scope_id')); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="action" value="save" />

		<?php echo JHTML::_('form.token'); ?>
	</form>
</section>