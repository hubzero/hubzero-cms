<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die( 'Restricted access' );

$this->view('default_menu')
     ->set('option', $this->option)
     ->set('controller', $this->controller)
     ->set('course', $this->course)
     ->set('offering', $this->offering)
     ->set('page', $this->model)
     ->set('pages', $this->pages)
     ->display();

$base = $this->offering->link() . '&active=pages';
?>

<div class="pages-wrap">
	<div class="pages-content">

	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } ?>

		<form action="<?php echo JRoute::_($base); ?>" method="post" id="pageform" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo ($this->model->exists()) ? JText::_('PLG_COURSES_PAGES_EDIT_PAGE') : JText::_('PLG_COURSES_PAGES_NEW_PAGE'); ?></legend>

				<div class="grid">
					<div class="col span-half">
						<label for="field-title">
							<?php echo JText::_('PLG_COURSES_PAGES_FIELD_TITLE'); ?> <span class="required"><?php echo JText::_('PLG_COURSES_PAGES_REQUIRED'); ?></span>
							<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
							<span class="hint"><?php echo JText::_('PLG_COURSES_PAGES_FIELD_TITLE_HINT'); ?></span>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-url">
							<?php echo JText::_('PLG_COURSES_PAGES_FIELD_ALIAS'); ?> <span class="optional"><?php echo JText::_('PLG_COURSES_PAGES_OPTINAL'); ?></span>
							<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->model->get('url'))); ?>" />
							<span class="hint"><?php echo JText::_('PLG_COURSES_PAGES_FIELD_ALIAS_HINT'); ?></span>
						</label>
					</div>
				</div>

			<?php if ($this->offering->access('manage')) { ?>
				<label for="field-section_id">
					<?php echo JText::_('PLG_COURSES_PAGES_FIELD_SECTION'); ?>
					<select name="fields[section_id]" id="field-section_id">
						<option value="0"<?php if ($this->model->get('section_id') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_COURSES_PAGES_FIELD_OPT_ALL_SECTIONS'); ?></option>
					<?php foreach ($this->offering->sections() as $section) { ?>
						<option value="<?php echo $section->get('id'); ?>"<?php if ($section->get('id') == $this->model->get('section_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php } ?>
					</select>
				</label>
			<?php } ?>

				<label for="fields_content">
					<?php echo JText::_('PLG_COURSES_PAGES_FIELD_CONTENT'); ?> <span class="required"><?php echo JText::_('PLG_COURSES_PAGES_REQUIRED'); ?></span>
					<?php
						echo \JFactory::getEditor()->display('fields[content]', $this->escape(stripslashes($this->model->get('content'))), '', '', 35, 50, false, 'field_content');
					?>
				</label>

				<div class="field-wrap">
					<div class="grid file-manager">
						<div class="col span-half">
							<div id="file-uploader" data-action="<?php echo JRoute::_($base . '&action=upload&no_html=1&section_id='); ?>" data-section="<?php echo $this->model->get('section_id'); ?>" data-list="<?php echo JRoute::_($base . '&action=list&no_html=1&section_id='); ?>">
								<iframe width="100%" height="370" name="filer" id="filer" style="border:2px solid #eee;margin-top: 0;" src="<?php echo JRoute::_($base . '&action=list&tmpl=component&page=' . $this->model->get('id') . '&section_id=' . $this->model->get('section_id')); ?>"></iframe>
							</div>
						</div>
						<div class="col span-half omega">
							<div id="file-uploader-list"></div>
						</div>
					</div>
				</div>

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo JText::_('PLG_COURSES_PAGES_SAVE'); ?>" />
					<a class="btn btn-secondary" href="<?php echo JRoute::_($base); ?>">
						<?php echo JText::_('PLG_COURSES_PAGES_CANCEL'); ?>
					</a>
				</p>
			</fieldset>

			<input type="hidden" name="fields[active]" value="<?php echo $this->model->get('active', 1); ?>" />
			<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
		<?php if ($this->offering->access('manage', 'section') && !$this->offering->access('manage')) { ?>
			<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />
		<?php } ?>
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />

			<?php echo JHTML::_('form.token'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="active" value="pages" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
		</form>

		<div class="clear"></div>
	</div><!-- / .below section -->
</div>