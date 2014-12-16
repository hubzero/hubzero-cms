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
defined('_JEXEC') or die( 'Restricted access' );

//$juser = JFactory::getUser();
/*if ($this->entry->id) {
	$lid = $this->entry->id;
} else {
	$lid = time().rand(0,10000);
}*/
$this->css()
     ->css('jquery.ui.css', 'system')
     ->js('jquery.timepicker.js', 'system')
     ->js();

if ($this->entry->get('publish_down') && $this->entry->get('publish_down') == '0000-00-00 00:00:00')
{
	$this->entry->set('publish_down', '');
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p><a class="icon-archive archive btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('COM_BLOG_ARCHIVE'); ?></a></p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
		<?php /*if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php }*/ ?>

		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=save'); ?>" method="post" id="hubForm">
			<div class="explaination">
				<h4 id="files-header"><?php echo JText::_('COM_BLOG_FIELD_FILES'); ?></h4>
				<iframe width="100%" height="370" name="filer" id="filer" src="<?php echo JRoute::_('index.php?option=' . $this->option . '&tmpl=component&controller=media'); ?>"></iframe>
			</div>
			<fieldset>
				<legend><?php echo JText::_('COM_BLOG_EDIT_DETAILS'); ?></legend>

				<label for="field-title"<?php if ($this->task == 'save' && !$this->entry->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
					<?php echo JText::_('COM_BLOG_FIELD_TITLE'); ?> <span class="required"><?php echo JText::_('COM_BLOG_REQUIRED'); ?></span>
					<input type="text" name="entry[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>" />
				</label>
				<?php if ($this->task == 'save' && !$this->entry->get('title')) { ?>
					<p class="error"><?php echo JText::_('COM_BLOG_ERROR_PROVIDE_TITLE'); ?></p>
				<?php } ?>

				<label for="entrycontent"<?php if ($this->task == 'save' && !$this->entry->get('content')) { echo ' class="fieldWithErrors"'; } ?>>
					<?php echo JText::_('COM_BLOG_FIELD_CONTENT'); ?> <span class="required"><?php echo JText::_('COM_BLOG_REQUIRED'); ?></span>
					<?php echo $this->editor('entry[content]', $this->escape($this->entry->content('raw')), 50, 40); ?>
				</label>
				<?php if ($this->task == 'save' && !$this->entry->get('content')) { ?>
					<p class="error"><?php echo JText::_('COM_BLOG_ERROR_PROVIDE_CONTENT'); ?></p>
				<?php } ?>

				<label>
					<?php echo JText::_('COM_BLOG_FIELD_TAGS'); ?>
					<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->entry->tags('string'))); ?>
					<span class="hint"><?php echo JText::_('COM_BLOG_FIELD_TAGS_HINT'); ?></span>
				</label>

				<div class="grid">
					<div class="col span-half">
						<label for="field-allow_comments">
							<input type="checkbox" class="option" name="entry[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->entry->get('allow_comments') == 1) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('COM_BLOG_FIELD_ALLOW_COMMENTS'); ?>
						</label>
					</div>

					<div class="col span-half omega">
						<label for="field-state">
							<?php echo JText::_('COM_BLOG_FIELD_PRIVACY'); ?>
							<select name="entry[state]" id="field-state">
								<option value="1"<?php if ($this->entry->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_PRIVACY_PUBLIC'); ?></option>
								<option value="2"<?php if ($this->entry->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_PRIVACY_REGISTERED'); ?></option>
								<option value="0"<?php if ($this->entry->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_PRIVACY_PRIVATE'); ?></option>
							</select>
						</label>
					</div>
				</div>

				<div class="grid">
					<div class="col span-half">
						<label for="field-publish_up">
							<?php echo JText::_('COM_BLOG_FIELD_PUBLISH_UP'); ?>
							<input type="text" name="entry[publish_up]" class="datetime-field" id="field-publish_up" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(JFactory::getConfig()->get('offset')), JDate::getInstance('now')) / 60); ?>" value="<?php echo ($this->entry->get('publish_up') ? $this->escape(JHTML::_('date', $this->entry->get('publish_up'), 'Y-m-d H:i:s')) : ''); ?>" />
							<span class="hint"><?php echo JText::_('COM_BLOG_FIELD_PUBLISH_HINT'); ?></span>
						</label>
					</div>

					<div class="col span-half omega">
						<label for="field-publish_down">
							<?php echo JText::_('COM_BLOG_FIELD_PUBLISH_DOWN'); ?>
							<input type="text" name="entry[publish_down]" class="datetime-field" id="field-publish_down" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(JFactory::getConfig()->get('offset')), JDate::getInstance('now')) / 60); ?>" value="<?php echo ($this->entry->get('publish_down') ? $this->escape(JHTML::_('date', $this->entry->get('publish_down'), 'Y-m-d H:i:s')) : ''); ?>" />
							<span class="hint"><?php echo JText::_('COM_BLOG_FIELD_PUBLISH_HINT'); ?></span>
						</label>
					</div>
				</div>
			</fieldset>
			<div class="clear"></div>

			<input type="hidden" name="lid" value="<?php //echo $lid; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->entry->get('created_by'); ?>" />
			<input type="hidden" name="entry[id]" value="<?php echo $this->entry->get('id'); ?>" />
			<input type="hidden" name="entry[alias]" value="<?php echo $this->entry->get('alias'); ?>" />
			<input type="hidden" name="entry[created]" value="<?php echo $this->entry->get('created'); ?>" />
			<input type="hidden" name="entry[created_by]" value="<?php echo $this->entry->get('created_by'); ?>" />
			<input type="hidden" name="entry[scope]" value="site" />
			<input type="hidden" name="entry[scope_id]" value="0" />
			<input type="hidden" name="entry[access]" value="0" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />

			<?php echo JHTML::_('form.token'); ?>

			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_BLOG_SAVE'); ?>" />

				<a class="btn btn-secondary" href="<?php echo $this->entry->get('id') ? JRoute::_($this->entry->link()) : JRoute::_('index.php?option=' . $this->option); ?>">
					<?php echo JText::_('COM_BLOG_CANCEL'); ?>
				</a>
			</p>
		</form>
	</div><!-- / .section-inner -->
</section><!-- / .section -->
