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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.editor');

// Change title depending on whether or not we're editing or creating a new billboard
$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

// Menu items
JToolBarHelper::title(JText::_('COM_BILLBOARDS_MANAGER') . ': ' . $text, 'addedit.png');

$bar = JToolBar::getInstance('toolbar');
// Add an upload button.
$bar->appendButton('Popup', 'upload', JText::_('COM_BILLBOARDS_IMAGES'), 'index.php?option=com_media&view=images&tmpl=component&folder='.$this->media_path, 640, 520);

JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('billboard');
?>

<script type="text/javascript">
function submitbutton(pressbutton) {
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// Do field validation:
	// Make sure there's a billboard name and that there's a css class if there's CSS
	if ($('#billboardname').val() == "") {
		alert("<?php echo JText::_('COM_BILLBOARDS_MUST_HAVE_A_NAME', true); ?>");
	} else {
		submitform(pressbutton);
	}
}

// @TODO: should probably put this somewhere else
jQuery(document).ready(function($){
	var styling        = $('#styling');
	var styling_table  = $('#styling_table');
	var slider         = styling_table.hide();

	styling.on('click', function(e) {
		e.preventDefault();
		slider.toggle();
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_BILLBOARDS_CONTENT'); ?></span></legend>

			<div class="input-wrap">
				<label for="billboardname"><?php echo JText::_('COM_BILLBOARDS_FIELD_NAME'); ?>:</label><br />
				<input type="text" name="billboard[name]" id="billboardname" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="billboardcollection"><?php echo JText::_('COM_BILLBOARDS_FIELD_COLLECTION'); ?>:</label><br />
				<?php echo $this->clist; ?>
			</div>
			<div class="input-wrap">
				<label for="ordering"><?php echo JText::_('COM_BILLBOARDS_FIELD_ORDERING'); ?>:</label><br />
				<?php echo $this->row->ordering; ?>
			</div>
			<div class="input-wrap">
				<label for="billboardheader"><?php echo JText::_('COM_BILLBOARDS_FIELD_HEADER'); ?>:</label><br />
				<input type="text" name="billboard[header]" id="billboardheader" value="<?php echo $this->escape(stripslashes($this->row->header)); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="billboardbackgroundimg"><?php echo JText::_('COM_BILLBOARDS_FIELD_BACKGROUND_IMG'); ?>:</label><br />
				<?php echo $this->image_location; ?>
				<input type="text" name="billboard[background_img]" id="billboardbackgroundimg" value="<?php echo $this->escape(stripslashes($this->row->background_img)); ?>" size="25" />
				<a class="modal" href="<?php echo JRoute::_('index.php?option=com_media&view=images&tmpl=component&folder=' . $this->media_path); ?>" data-rel="{handler: 'iframe', size: {x: 640, y: 520}}"><?php echo JText::_('COM_BILLBOARDS_UPLOAD_IMAGE'); ?></a>
			</div>
			<div class="input-wrap">
				<label for="billboard[text]"><?php echo JText::_('COM_BILLBOARDS_FIELD_TEXT'); ?>:</label><br />
				<?php
					echo JEditor::getInstance()->display('billboard[text]', $this->escape(stripslashes($this->row->text)), '', '', 45, 13, false);
				?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_BILLBOARDS_LEARN_MORE'); ?></span></legend>
			<div class="input-wrap">
				<label for="billboardlearnmoretext"><?php echo JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_TEXT'); ?>:</label><br />
				<input type="text" name="billboard[learn_more_text]" id="billboardlearnmoretext" value="<?php echo $this->escape(stripslashes($this->row->learn_more_text)); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="billboardlearnmoretarget"><?php echo JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_TARGET'); ?>:</label><br />
				<input type="text" name="billboard[learn_more_target]" id="billboardlearnmoretarget" value="<?php echo $this->escape(stripslashes($this->row->learn_more_target)); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="billboardlearnmoreclass"><?php echo JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_CLASS'); ?>:</label><br />
				<input type="text" name="billboard[learn_more_class]" id="billboardlearnmoreclass" value="<?php echo $this->escape(stripslashes($this->row->learn_more_class)); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="billboardlearnmorelocation"><?php echo JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION'); ?>:</label><br />
				<?php echo $this->learnmorelocation; ?>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<!-- @TODO: remove inline styles -->
			<legend id="styling" style="cursor:pointer;"><?php echo JText::_('COM_BILLBOARDS_STYLING'); ?></legend>
			<br style="clear:both;" />

			<div id="styling_table">
				<div class="input-wrap">
					<label for="billboardalias"><?php echo JText::_('COM_BILLBOARDS_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="billboard[alias]" id="billboardalias" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="billboardpadding"><?php echo JText::_('COM_BILLBOARDS_FIELD_PADDING'); ?>:</label><br />
					<input type="text" name="billboard[padding]" id="billboardpadding" value="<?php echo $this->escape(stripslashes($this->row->padding)); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="billboard[css]"><?php echo JText::_('COM_BILLBOARDS_FIELD_CSS'); ?>:</label><br />
					<?php
						echo JEditor::getInstance()->display('billboard[css]', $this->escape(stripslashes($this->row->css)), '', '', 45, 13, false);
					?>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="billboard[id]" value="<?php echo $this->row->id; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
