<?php 
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Wiki_Parser');
$view = new Hubzero_Plugin_View(
	array(
		'folder'  => 'courses',
		'element' => 'pages',
		'name'    => 'pages',
		'layout'  => 'default_menu'
	)
);
$view->option     = $this->option;
$view->controller = $this->controller;
$view->course     = $this->course;
$view->offering   = $this->offering;
$view->page       = $this->model;
$view->pages      = $this->pages;
$view->display();

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=pages';
?>

<div class="pages-wrap">
	<div class="pages-content">

	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } ?>

		<form action="<?php echo JRoute::_($base); ?>" method="post" id="pageform" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo ($this->model->exists()) ? JText::_('Edit page') : JText::_('New page'); ?></legend>

				<div class="two columns first">
					<label for="field-title">
						<?php echo JText::_('Title:'); ?> <span class="required"><?php echo JText::_('required'); ?></span>
						<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
						<span class="hint"><?php echo JText::_('Titles should be relatively short and succinct. Usually one to three words is preferred.'); ?></span>
					</label>
				</div>
				<div class="two columns second">
					<label for="field-url">
						<?php echo JText::_('URL:'); ?> <span class="optional"><?php echo JText::_('optional'); ?></span>
						<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->model->get('url'))); ?>" />
						<span class="hint"><?php echo JText::_('URLs can only contain alphanumeric characters and underscores. Spaces will be removed.'); ?></span>
					</label>
				</div>
				<div class="clear"></div>

			<?php if ($this->offering->access('manage')) { ?>
				<label for="field-section_id">
					<?php echo JText::_('Page appears for section:'); ?>
					<select name="fields[section_id]" id="field-section_id">
						<option value="0"<?php if ($this->model->get('section_id') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('- All sections -'); ?></option>
					<?php foreach ($this->offering->sections() as $section) { ?>
						<option value="<?php echo $section->get('id'); ?>"<?php if ($section->get('id') == $this->model->get('section_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php } ?>
					</select>
				</label>
			<?php } ?>

				<label for="fields_content">Content: <span class="required"><?php echo JText::_('required'); ?></span>
					<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('fields[content]', 'field_content', stripslashes($this->model->get('content')), '', '50', '50');
					?>
					<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>"><?php echo JText::_('Wiki formatting'); ?></a> &amp; <a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros'); ?>">Wiki Macros</a> are allowed.</span>
				</label>

				<div class="field-wrap">
					<div class="grid">
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
					<input type="submit" value="<?php echo JText::_('Save'); ?>" />
				</p>
			</fieldset>

			<input type="hidden" name="fields[active]" value="<?php echo $this->model->get('active', 1); ?>" />
			<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
		<?php if ($this->offering->access('manage', 'section') && !$this->offering->access('manage')) { ?>
			<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />
		<?php } ?>
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="active" value="pages" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : ''); ?>" />
		</form>

		<div class="clear"></div>
	</div><!-- / .below section -->
</div>