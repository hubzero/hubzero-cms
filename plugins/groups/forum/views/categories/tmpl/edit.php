<?php 
defined('_JEXEC') or die( 'Restricted access' );
$juser = JFactory::getUser();

ximport('Hubzero_User_Profile_Helper');
?>
<ul id="page_options">
	<li>
		<a class="categories btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'); ?>">
			<?php echo JText::_('All categories'); ?>
		</a>
	</li>
</ul>

<div class="main section">
<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'); ?>" method="post" id="hubForm" class="full">
			<fieldset>
				<legend class="post-comment-title">
			<?php if ($this->model->id) { ?>
					<?php echo JText::_('PLG_GROUPS_FORUM_EDIT_CATEGORY'); ?>
			<?php } else { ?>
					<?php echo JText::_('PLG_GROUPS_FORUM_NEW_CATEGORY'); ?>
			<?php } ?>
				</legend>
				
				<div class="group">
					<label for="field-section_id">
						<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_SECTION'); ?>
						<select name="fields[section_id]" id="field-section_id">
							<option value="0"><?php echo JText::_('PLG_GROUPS_FORUM_FIELD_SECTION_SELECT'); ?></option>
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
				
					<label for="field-closed" id="comment-anonymous-label">
						<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_LOCKED'); ?><br />
						<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="3"<?php if ($this->model->closed) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_CLOSED'); ?>
					</label>
				</div>
				
				<label for="field-title">
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->title)); ?>" />
				</label>
				
				<label for="field-description">
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>
					<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->model->description)); ?></textarea>
				</label>

				<div class="sidenote">
					<p>
						<?php echo JText::_('PLG_GROUPS_FORUM_CATEGORY_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
			</p>
			
			<input type="hidden" name="fields[alias]" value="<?php echo $this->model->alias; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[group_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="task" value="savecategory" />
		</form>

	<div class="clear"></div>
</div><!-- / .main section -->