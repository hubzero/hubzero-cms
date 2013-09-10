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

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

ximport('Hubzero_User_Profile_Helper');
$juser = JFactory::getUser();

$no_html = JRequest::getInt('no_html', 0);

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=' . $this->name;
if ($this->post->id) {
	$action = $base . '&unit=' . $this->category->alias . '&b=' . $this->post->id;
} else {
	$action = $base . '&unit=' . $this->category->alias;
}
?>
	<form action="<?php echo JRoute::_($base); ?>" method="post" id="commentform" class="comment-edit" enctype="multipart/form-data" data-thread="<?php echo $this->post->get('thread'); ?>">
	<?php if (!$no_html) { ?>
		<p class="comment-member-photo">
			<a class="comment-anchor" name="commentform"></a>
			<?php
			$anone = 1;
			if (!$juser->get('guest')) 
			{
				$anon = 0;
			}
			$now = date('Y-m-d H:i:s', time());
			?>
			<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($juser, $anon); ?>" alt="<?php echo JText::_('User photo'); ?>" />
		</p>
	

		<fieldset>
	<?php } ?>
	<?php if ($juser->get('guest')) { ?>
			<p class="warning"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'); ?></p>
	<?php } else { ?>
			<?php if (!$no_html) { ?>
			<p class="comment-title">
				<strong>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
				</strong> 
				<span class="permalink">
					<span class="comment-date-at">@</span>
					<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></time></span>
				</span>
			</p>
			<?php } ?>

			<label for="field_comment">
				<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
				<?php
				ximport('Hubzero_Wiki_Editor');
				$editor = Hubzero_Wiki_Editor::getInstance();
				echo $editor->display('fields[comment]', 'field_comment', $this->post->get('comment'), 'minimal no-footer', '35', '5');
				?>
			</label>
		<?php if (!$this->post->get('parent')) { ?>
			<div class="grid">
				<div class="col span-half">
		<?php } ?>
			<label for="field-upload" id="comment-upload">
				<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?>:</span>
				<input type="file" name="upload" id="field-upload" />
			</label>
		<?php if (!$this->post->get('parent')) { ?>
				</div>
				<div class="col span-half omega">
					<label for="field-category_id">
						<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY'); ?></span>
						<select name="fields[category_id]" id="field-category_id">
							<option value="0"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY_SELECT'); ?></option>
						<?php
						foreach ($this->sections as $section)
						{
							if ($section->categories) 
							{
						?>
							<optgroup label="<?php echo $this->escape(stripslashes($section->title)); ?>">
							<?php foreach ($section->categories as $category) { ?>
								<option value="<?php echo $category->id; ?>"<?php if ($category->id == $this->post->get('category_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->title)); ?></option>
							<?php } ?>
							</optgroup>
						<?php
							}
						}
						?>
					</select>
				</label>
				</div>
			</div>
		<?php } else { ?>
			<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
		<?php } ?>

			<label for="field-anonymous" id="comment-anonymous-label">
				<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous"<?php if ($this->post->get('anonymous') == 1) { echo ' checked="checked"'; } ?> value="1" /> 
				<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
			</label>

			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
			</p>
	<?php } ?>
	<?php if (!$no_html) { ?>
		</fieldset>
	<?php } ?>
		<input type="hidden" name="fields[parent]" id="field-parent" value="<?php echo $this->post->get('parent'); ?>" />
		<input type="hidden" name="fields[state]" id="field-state" value="<?php echo $this->post->get('state'); ?>" />
		<input type="hidden" name="fields[scope]" id="field-scope" value="<?php echo $this->post->get('scope', 'course'); ?>" />
		<input type="hidden" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->post->get('scope_id'); ?>" />
		<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->post->get('id'); ?>" />
		<input type="hidden" name="fields[object_id]" id="field-object_id" value="<?php echo $this->post->get('object_id'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
		<input type="hidden" name="active" value="discussions" />
		<input type="hidden" name="action" value="savethread" />

		<input type="hidden" name="section" value="<?php //echo $this->filters['section']; ?>" />
		<input type="hidden" name="return" value="<?php //echo base64_encode(JRoute::_($base . '&active=outline&unit=' . $this->filters['section'] . '&b=' . $this->category->alias)); ?>" />
	<?php if (!$no_html) { ?>
		<p class="instructions">
			Click on a comment on the left to view a discussion or start your own above.
		</p>
	<?php } ?>
	</form>