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

defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();
?>
<section class="main section">
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<h3 class="post-comment-title">
		<?php if ($this->model->id) { ?>
			<?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDIT_CATEGORY'); ?>
		<?php } else { ?>
			<?php echo JText::_('PLG_COURSES_DISCUSSIONS_NEW_CATEGORY'); ?>
		<?php } ?>
		</h3>

		<form action="<?php echo JRoute::_($this->offering->link() . '&active=discussions'); ?>" method="post" id="commentform">
			<p class="comment-member-photo">
				<?php
				$jxuser = new \Hubzero\User\Profile();
				$jxuser->load($juser->get('id'));
				?>
				<img src="<?php echo $jxuser->getPicture(); ?>" alt="" />
			</p>

			<fieldset>
				<label for="field-section_id">
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_SECTION'); ?> <span class="required"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_REQUIRED'); ?></span>
					<select name="fields[section_id]" id="field-section_id">
						<option value="0"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_SECTION_SELECT'); ?></option>
					<?php foreach ($this->sections as $section) { ?>
						<option value="<?php echo $section->id; ?>"<?php if ($this->model->section_id == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
					<?php } ?>
					</select>
				</label>

				<label for="field-title">
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_TITLE'); ?> <span class="required"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_REQUIRED'); ?></span>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->title)); ?>" />
				</label>

				<label for="field-description">
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_DESCRIPTION'); ?>
					<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->model->description)); ?></textarea>
				</label>

				<label for="field-closed" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="3"<?php if ($this->model->closed) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_CLOSED'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
				</p>
			</fieldset>
			<input type="hidden" name="fields[alias]" value="<?php echo $this->model->alias; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->offering->get('id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
			<input type="hidden" name="active" value="discussions" />
			<input type="hidden" name="unit" value="manage" />
			<input type="hidden" name="action" value="savecategory" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_CATEGORY_HINT'); ?></p>
	</aside><!-- /.aside -->
</section><!-- / .main section -->