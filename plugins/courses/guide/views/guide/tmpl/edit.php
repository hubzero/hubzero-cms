<?php 
defined('_JEXEC') or die( 'Restricted access' );

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=' . $this->plugin;
?>

<div class="pages-wrap">
	<div class="pages-content">

	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } ?>

		<form action="<?php echo JRoute::_($base); ?>" method="post" id="pageform" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo ($this->model->exists()) ? JText::_('Edit page') : JText::_('New page'); ?></legend>

				<label for="field-title">
					<?php echo JText::_('Title:'); ?> <span class="required"><?php echo JText::_('required'); ?></span>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
					<span class="hint"><?php echo JText::_('Titles should be relatively short and succinct. Usually one to three words is preferred.'); ?></span>
				</label>

				<input type="hidden" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->model->get('url'))); ?>" />

				<label for="fields_content">Content: <span class="required"><?php echo JText::_('required'); ?></span>
					<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('fields[content]', 'field_content', stripslashes($this->model->get('content')), '', '50', '50');
					?>
					<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>"><?php echo JText::_('Wiki formatting'); ?></a> &amp; <a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros'); ?>">Wiki Macros</a> are allowed.</span>
				</label>

				<div class="field-wrap">
				<div class="grid">
					<div class="col span-half">
						<div id="file-uploader" data-action="<?php echo JRoute::_($base . '&action=upload&no_html=1'); ?>" data-list="<?php echo JRoute::_($base . '&action=list&no_html=1'); ?>">
							<iframe width="100%" height="370" name="filer" id="filer" style="border:2px solid #eee;margin-top: 0;" src="<?php echo JRoute::_($base . '&action=list&tmpl=component'); ?>"></iframe>
						</div>
					</div>
					<div class="col span-half omega">
						<div id="file-uploader-list"></div>
					</div>
				</div>
			</div>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('Save'); ?>" />
				</p>
			</fieldset>

			<input type="hidden" name="fields[active]" value="<?php echo $this->model->get('active', 1); ?>" />
			<input type="hidden" name="fields[offering_id]" value="<?php echo $this->model->get('offering_id'); ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->model->get('course_id'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="active" value="<?php echo $this->plugin; ?>" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : ''); ?>" />
		</form>

		<div class="clear"></div>
	</div><!-- / .below section -->
</div>