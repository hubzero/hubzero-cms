<?php 
defined('_JEXEC') or die( 'Restricted access' );
$juser = JFactory::getUser();

ximport('Hubzero_User_Profile_Helper');
?>
<div class="main section">
<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>
	<h3 class="post-comment-title">
<?php if ($this->model->id) { ?>
		<?php echo JText::_('PLG_COURSES_FORUM_EDIT_CATEGORY'); ?>
<?php } else { ?>
		<?php echo JText::_('PLG_COURSES_FORUM_NEW_CATEGORY'); ?>
<?php } ?>
	</h3>			
	<div class="aside">
		<table class="wiki-reference" summary="<?php echo JText::_('PLG_COURSES_FORUM_WIKI_SYNTAX_REFERENCE'); ?>">
			<caption><?php echo JText::_('PLG_COURSES_FORUM_WIKI_SYNTAX_REFERENCE'); ?></caption>
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
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('cn') . '&active=forum'); ?>" method="post" id="commentform">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
<?php
				$jxuser = new Hubzero_User_Profile();
				$jxuser->load($juser->get('id'));
				$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
	
			<fieldset>
				<label for="field-section_id">
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_SECTION'); ?>
					<select name="fields[section_id]" id="field-section_id">
						<option value="0"><?php echo JText::_('PLG_COURSES_FORUM_FIELD_SECTION_SELECT'); ?></option>
<?php
				foreach ($this->sections as $section)
				{
?>
						<option value="<?php echo $section->id; ?>"<?php if ($this->model->section_id == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
<?php
				}
?>
					</select>
				</label>
				
				<label for="field-title">
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->title)); ?>" />
				</label>
				
				<label for="field-description">
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_DESCRIPTION'); ?>
					<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->model->description)); ?></textarea>
				</label>
		
				<label for="field-closed" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="3"<?php if ($this->model->closed) { echo ' checked="checked"'; } ?> /> 
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_CLOSED'); ?>
				</label>
		
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_COURSES_FORUM_SUBMIT'); ?>" />
				</p>
		
				<div class="sidenote">
					<p>
						<?php echo JText::_('PLG_COURSES_FORUM_CATEGORY_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[alias]" value="<?php echo $this->model->alias; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[group_id]" value="<?php echo $this->course->get('gidNumber'); ?>" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('cn'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="task" value="savecategory" />
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->