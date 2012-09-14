<?php 
defined('_JEXEC') or die( 'Restricted access' );
$juser = JFactory::getUser();

if ($this->post->id) {
	$action = 'index.php?option=' . $this->option . '&section=' . $this->section->alias . '&category=' . $this->category->alias . '&task=edit&thread=' . $this->post->id;
} else {
	$action = 'index.php?option=' . $this->option . '&section=' . $this->section->alias . '&category=' . $this->category->alias . '&task=new';
}
?>
<div id="content-header">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>
</div>
<div id="content-header-extra">
	<p><a class="comments btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->section->alias . '&category=' . $this->category->alias); ?>"><?php echo JText::_('All discussions'); ?></a></p>
</div>
<div class="clear"></div>

<?php
	foreach($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<div class="main section">
	<h3 class="post-comment-title">
<?php if ($this->post->id) { ?>
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
				$jxuser->load($juser->get('id'));
				$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
	
			<fieldset>
<?php if ($this->config->get('access-manage-thread') && !$this->post->parent) { ?>
				<label for="field-sticky">
					<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->post->sticky) { echo ' checked="checked"'; } ?> /> 
					<?php echo JText::_('COM_FORUM_FIELD_STICKY'); ?>
				</label>
<?php } else { ?>
				<input type="hidden" name="fields[sticky]" id="field-sticky" value="<?php echo $this->post->sticky; ?>" />
<?php } ?>
<?php if (!$this->post->parent) { ?>
				<label for="field-category_id">
					<?php echo JText::_('COM_FORUM_FIELD_CATEGORY'); ?>
					<select name="fields[category_id]" id="field-category_id">
						<option value="0"><?php echo JText::_('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
<?php
				foreach ($this->sections as $section)
				{
					if ($section->categories) 
					{
?>
						<optgroup label="<?php echo $this->escape(stripslashes($section->title)); ?>">
<?php
						foreach ($section->categories as $category)
						{
?>
						<option value="<?php echo $category->id; ?>"<?php if ($this->category->alias == $category->alias) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->title)); ?></option>
<?php
						}
?>
						</optgroup>
<?php
					}
				}
?>
					</select>
				</label>

				<label for="field-title">
					<?php echo JText::_('COM_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->post->title)); ?>" />
				</label>
<?php } else { ?>
				<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->category_id; ?>" />
<?php } ?>
				<label for="fieldcomment">
					<?php echo JText::_('COM_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'fieldcomment', stripslashes($this->post->comment), '', '35', '15');
					?>
				</label>
<?php //if (!$this->post->parent) { ?>
				<label>
					<?php echo JText::_('COM_FORUM_FIELD_TAGS'); ?>:
<?php 
		JPluginHelper::importPlugin('hubzero');
		$dispatcher = JDispatcher::getInstance();
		$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)) );
		if (count($tf) > 0) {
			echo $tf[0];
		} else {
			echo '<input type="text" name="tags" value="'. $this->tags .'" />';
		}
?>
				</label>
<?php //} ?>
				<fieldset>
					<legend><?php echo JText::_('COM_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('COM_FORUM_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>
				
				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->post->anonymous) { echo ' checked="checked"'; } ?> /> 
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
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->parent; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[group_id]" value="0" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->post->id; ?>" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="threads" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="section" value="<?php echo $this->section->alias; ?>" />
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->