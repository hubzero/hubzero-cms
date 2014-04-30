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

$juser = JFactory::getUser();

$this->category->set('section_alias', $this->section->get('alias'));
$this->post->set('section', $this->section->get('alias'));
$this->post->set('category', $this->category->get('alias'));

if ($this->post->exists()) 
{
	$action = $this->post->link('edit');
} 
else 
{
	$action = $this->post->link('new');
}
?>
<div id="content-header">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>
</div>
<div id="content-header-extra">
	<p><a class="icon-comments comments btn" href="<?php echo JRoute::_($this->category->link()); ?>"><?php echo JText::_('COM_FORUM_ALL_DISCUSSIONS'); ?></a></p>
</div>
<div class="clear"></div>

<?php
	foreach ($this->notifications as $notification) 
	{
		echo '<p class="' . $notification['type'] . '">' . $notification['message'] . '</p>';
	}
?>

<div class="main section">
	<h3 class="post-comment-title">
	<?php if ($this->post->exists()) { ?>
		<?php echo JText::_('COM_FORUM_EDIT_DISCUSSION'); ?>
	<?php } else { ?>
		<?php echo JText::_('COM_FORUM_NEW_DISCUSSION'); ?>
	<?php } ?>
	</h3>			
	<div class="aside">
		<div class="container">
			<p><strong><?php echo JText::_('COM_FORUM_WHAT_IS_STICKY'); ?></strong><br />
			<?php echo JText::_('COM_FORUM_STICKY_EXPLANATION'); ?></p>

			<p><strong><?php echo JText::_('COM_FORUM_WHAT_IS_LOCKING'); ?></strong><br />
			<?php echo JText::_('COM_FORUM_LOCKING_EXPLANATION'); ?></p>
		</div>
	</div><!-- /.aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_($action); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
				<?php
				$jxuser = new \Hubzero\User\Profile();
				$jxuser->load($this->post->get('created_by', $juser->get('id')));
				?>
				<img src="<?php echo $jxuser->getPicture(); ?>" alt="" />
			</p>

			<fieldset>
			<?php if ($this->config->get('access-manage-thread') && !$this->post->get('parent')) { ?>
				<div class="grid">
					<div class="col span-half">
						<label for="field-sticky">
							<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->post->get('sticky')) { echo ' checked="checked"'; } ?> /> 
							<?php echo JText::_('COM_FORUM_FIELD_STICKY'); ?>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-closed">
							<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="1"<?php if ($this->post->get('closed')) { echo ' checked="checked"'; } ?> /> 
							<?php echo JText::_('COM_FORUM_FIELD_CLOSED_THREAD'); ?>
						</label>
					</div>
				</div>
			<?php } else { ?>
				<input type="hidden" name="fields[sticky]" id="field-sticky" value="<?php echo $this->post->get('sticky'); ?>" />
				<input type="hidden" name="fields[closed]" id="field-closed" value="<?php echo $this->post->get('closed'); ?>" />
			<?php } ?>

			<?php if (!$this->post->get('parent')) { ?>
				<label for="field-category_id">
					<?php echo JText::_('COM_FORUM_FIELD_CATEGORY'); ?> <span class="required"><?php echo JText::_('COM_FORUM_REQUIRED'); ?></span>
					<select name="fields[category_id]" id="field-category_id">
				<?php foreach ($this->model->sections() as $section) { ?>
					<?php if ($section->categories('list')->total() > 0) { ?>
						<optgroup label="<?php echo $this->escape(stripslashes($section->get('title'))); ?>">
						<?php foreach ($section->categories() as $category) { ?>
							<option value="<?php echo $category->get('id'); ?>"<?php if ($this->category->get('alias') == $category->get('alias')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->get('title'))); ?></option>
						<?php } ?>
						</optgroup>
					<?php } ?>
				<?php } ?>
					</select>
				</label>

				<label for="field-title">
					<?php echo JText::_('COM_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->post->get('title'))); ?>" />
				</label>
			<?php } else { ?>
				<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
			<?php } ?>

				<label for="fieldcomment">
					<?php echo JText::_('COM_FORUM_FIELD_COMMENTS'); ?> <span class="required"><?php echo JText::_('COM_FORUM_REQUIRED'); ?></span>
					<?php
					echo \JFactory::getEditor()->display('fields[comment]', $this->escape(stripslashes($this->post->content('raw'))), '', '', 35, 15, false, 'fieldcomment', null, null, array('class' => 'minimal no-footer'));
					?>
				</label>

				<label>
					<?php echo JText::_('COM_FORUM_FIELD_TAGS'); ?>:
					<?php 
						JPluginHelper::importPlugin('hubzero');
						$tf = JDispatcher::getInstance()->trigger(
							'onGetMultiEntry', 
							array(array('tags', 'tags', 'actags', '', $this->post->tags('string')))
						);

						echo (count($tf) > 0) ? implode("\n", $tf) : '<input type="text" name="tags" id="actags" value="'. $this->escape($this->post->tags('string')) .'" />';
					?>
				</label>

				<fieldset>
					<legend><?php echo JText::_('COM_FORUM_LEGEND_ATTACHMENTS'); ?></legend>

					<div class="grid">
						<div class="col span-half">
							<label for="upload">
								<?php echo JText::_('COM_FORUM_FIELD_FILE'); ?> <?php if ($this->post->attachment()->get('filename')) { echo '<strong>' . $this->escape(stripslashes($this->post->attachment()->get('filename'))) . '</strong>'; } ?>
								<input type="file" name="upload" id="upload" />
							</label>
						</div>
						<div class="col span-half omega">
							<label for="field-attach-descritpion">
								<?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?>
								<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($this->post->attachment()->get('description'))); ?>" />
							</label>
						</div>
						<input type="hidden" name="attachment" value="<?php echo $this->escape(stripslashes($this->post->attachment()->get('id'))); ?>" />
					</div>
					<?php if ($this->post->attachment()->exists()) { ?>
						<p class="warning">
							<?php echo JText::_('COM_FORUM_FIELD_FILE_WARNING'); ?>
						</p>
					<?php } ?>
				</fieldset>
				
				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->post->get('anonymous')) { echo ' checked="checked"'; } ?> /> 
					<?php echo JText::_('COM_FORUM_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('COM_FORUM_SUBMIT'); ?>" />
				</p>

				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('COM_FORUM_KEEP_POLITE'); ?></strong>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->get('parent'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[access]" value="0" />
			<input type="hidden" name="fields[thread]" value="<?php echo $this->post->get('thread'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->post->get('id'); ?>" />
			<input type="hidden" name="fields[scope]" value="site" />
			<input type="hidden" name="fields[scope_id]" value="0" />
			<input type="hidden" name="fields[scope_sub_id]" value="<?php echo $this->post->get('scope_sub_id'); ?>" />
			<input type="hidden" name="fields[object_id]" value="<?php echo $this->post->get('object_id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="threads" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="section" value="<?php echo $this->escape($this->section->get('alias')); ?>" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->