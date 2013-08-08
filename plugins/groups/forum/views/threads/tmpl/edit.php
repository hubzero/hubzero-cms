<?php 
defined('_JEXEC') or die( 'Restricted access' );
$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

if ($this->post->exists()) {
	$action = $base . '&scope=' . $this->section->get('alias') . '/' . $this->category->get('alias') . '/' . $this->post->get('thread');
} else {
	$action = $base . '&scope=' . $this->section->get('alias') . '/' . $this->category->get('alias');
}
?>
<ul id="page_options">
	<li>
		<a class="icon-comments comments btn" href="<?php echo JRoute::_($base . '&scope=' . $this->section->get('alias') . '/' . $this->category->get('alias')); ?>">
			<?php echo JText::_('All discussions'); ?>
		</a>
	</li>
</ul>

<div class="main section">
<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>
	
	<h3 class="post-comment-title">
	<?php if ($this->post->exists()) { ?>
		<?php echo JText::_('PLG_GROUPS_FORUM_EDIT_DISCUSSION'); ?>
	<?php } else { ?>
		<?php echo JText::_('PLG_GROUPS_FORUM_NEW_DISCUSSION'); ?>
	<?php } ?>
	</h3>			
	<div class="aside">
		<table class="wiki-reference" summary="<?php echo JText::_('PLG_GROUPS_FORUM_WIKI_SYNTAX_REFERENCE'); ?>">
			<caption><?php echo JText::_('PLG_GROUPS_FORUM_WIKI_SYNTAX_REFERENCE'); ?></caption>
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
				$jxuser->load($juser->get('id'));
				$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
				?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
	
			<fieldset>
			<?php if ($this->config->get('access-edit-thread') && !$this->post->get('parent')) { ?>
				<div class="grid">
					<div class="col span-half">
						<label for="field-sticky">
							<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->post->get('sticky')) { echo ' checked="checked"'; } ?> /> 
							<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_STICKY'); ?>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-closed">
							<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="1"<?php if ($this->post->get('closed')) { echo ' checked="checked"'; } ?> /> 
							<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_CLOSED_THREAD'); ?>
						</label>
					</div>
				</div>

				
			<?php } else { ?>
				<input type="hidden" name="fields[sticky]" id="field-sticky" value="<?php echo $this->post->get('sticky'); ?>" />
				<input type="hidden" name="fields[closed]" id="field-closed" value="<?php echo $this->post->get('closed'); ?>" />
			<?php } ?>

			<?php if (!$this->post->get('parent')) { ?>
				<label for="field-category_id">
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_CATEGORY'); ?>
					<select name="fields[category_id]" id="field-category_id">
						<option value="0"><?php echo JText::_('PLG_GROUPS_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
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
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->post->get('title'))); ?>" />
				</label>
			<?php } else { ?>
				<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
			<?php } ?>

				<label for="field_comment">
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'field_comment', stripslashes($this->post->get('comment')), 'no-file-macro no-image-macro', '35', '15');
					?>
				</label>

				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_TAGS'); ?>:
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
					<legend><?php echo JText::_('PLG_GROUPS_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grid">
						<div class="col span-half">
							<label for="upload">
								<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_FILE'); ?>:
								<input type="file" name="upload" id="upload" />
							</label>
						</div>
						<div class="col span-half omega">
							<label for="field-attach-descritpion">
								<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>:
								<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($this->post->attachment()->get('description'))); ?>" />
							</label>
						</div>
						<input type="hidden" name="attachment" value="<?php echo $this->escape(stripslashes($this->post->attachment()->get('id'))); ?>" />
					</div>
				</fieldset>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->post->get('anonymous')) { echo ' checked="checked"'; } ?> /> 
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
				</p>

				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('PLG_GROUPS_FORUM_KEEP_POLITE'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('PLG_GROUPS_FORUM_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->get('parent'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->post->get('id'); ?>" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->model->get('scope'); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->model->get('scope_id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->section->get('alias'); ?>" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->