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

$base = 'index.php?option=' . $this->option . '&section=' . $this->section->get('alias') . '&category=' . $this->category->get('alias');
if ($this->post->get('id')) {
	$action = $base . '&task=edit&thread=' . $this->post->get('id');
} else {
	$action = $base . '&task=new';
}
?>
<div id="content-header">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>
</div>
<div id="content-header-extra">
	<p><a class="icon-comments comments btn" href="<?php echo JRoute::_($base); ?>"><?php echo JText::_('All discussions'); ?></a></p>
</div>
<div class="clear"></div>

<?php
	foreach($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
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
		<table class="wiki-reference" summary="<?php echo JText::_('COM_FORUM_WIKI_SYNTAX_REFERENCE'); ?>">
			<caption><?php echo JText::_('COM_FORUM_WIKI_SYNTAX_REFERENCE'); ?></caption>
			<tbody>
				<tr>
					<td>'''bold'''</td>
					<td><b>bold</b></td>
				</tr>
				<tr>
					<td>''italic''</td>
					<td><i>italic</i></td>
				</tr>
				<tr>
					<td>__underline__</td>
					<td><span style="text-decoration:underline;">underline</span></td>
				</tr>
				<tr>
					<td>{{{monospace}}}</td>
					<td><code>monospace</code></td>
				</tr>
				<tr>
					<td>~~strike-through~~</td>
					<td><del>strike-through</del></td>
				</tr>
				<tr>
					<td>^superscript^</td>
					<td><sup>superscript</sup></td>
				</tr>
				<tr>
					<td>,,subscript,,</td>
					<td><sub>subscript</sub></td>
				</tr>
			</tbody>
		</table>
	</div><!-- /.aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_($action); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
				<?php
				ximport('Hubzero_User_Profile_Helper');
				$jxuser = new Hubzero_User_Profile();
				$jxuser->load($this->post->get('created_by', $juser->get('id')));
				$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
				?>
				<img src="<?php echo $thumb; ?>" alt="" />
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
					<?php echo JText::_('COM_FORUM_FIELD_CATEGORY'); ?>
					<select name="fields[category_id]" id="field-category_id">
						<option value="0"><?php echo JText::_('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
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
					<?php echo JText::_('COM_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'fieldcomment', stripslashes($this->post->get('comment')), '', '35', '15');
					?>
				</label>

				<label>
					<?php echo JText::_('COM_FORUM_FIELD_TAGS'); ?>:
					<?php 
						JPluginHelper::importPlugin('hubzero');
						$dispatcher = JDispatcher::getInstance();
						$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->post->tags('string'))) );
						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<input type="text" name="tags" value="'. $this->post->tags('string') .'" />';
						}
					?>
				</label>

				<fieldset>
					<legend><?php echo JText::_('COM_FORUM_LEGEND_ATTACHMENTS'); ?></legend>

					<div class="grid">
						<div class="col span-half">
							<label for="upload">
								<?php echo JText::_('COM_FORUM_FIELD_FILE'); ?>: <?php if ($this->post->attachment()->get('filename')) { echo '<strong>' . $this->escape(stripslashes($this->post->attachment()->get('filename'))) . '</strong>'; } ?>
								<input type="file" name="upload" id="upload" />
							</label>
						</div>
						<div class="col span-half omega">
							<label for="field-attach-descritpion">
								<?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?>:
								<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($this->post->attachment()->get('description'))); ?>" />
							</label>
						</div>
						<input type="hidden" name="attachment" value="<?php echo $this->escape(stripslashes($this->post->attachment()->get('id'))); ?>" />
					</div>
					<?php if ($this->post->attachment()->exists()) { ?>
						<p class="warning">
							Selecting a new file will replace the current file.
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
					<p>
						<?php echo JText::_('COM_FORUM_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->get('parent'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->post->get('id'); ?>" />
			<input type="hidden" name="fields[scope]" value="site" />
			<input type="hidden" name="fields[scope_id]" value="0" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="threads" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="section" value="<?php echo $this->section->get('alias'); ?>" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->