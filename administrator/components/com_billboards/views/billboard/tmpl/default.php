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
$text = ($this->task == 'edit' ? JText::_('BILLBOARDS_MANAGER_EDIT') : JText::_('BILLBOARDS_MANAGER_NEW'));

// Menu items
JToolBarHelper::title(JText::_('BILLBOARDS_MANAGER') . ': <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$bar = JToolBar::getInstance('toolbar');
	// Add an upload button.
	$bar->appendButton('Popup', 'upload', JText::_('BILLBOARD_IMAGES'), 'index.php?option=com_media&view=images&tmpl=component&folder='.$this->media_path, 640, 520);
}
else
{
	JToolBarHelper::media_manager($this->media_path, JText::_('BILLBOARD_IMAGES'));
}

JToolBarHelper::save();
JToolBarHelper::cancel();

if (version_compare(JVERSION, '1.6', 'ge'))
{
	JHtml::script(Juri::root() . 'media/system/js/mootools-more.js', true);
}

?>

<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// Do field validation: 
	// Make sure there's a billboard name and that there's a css class if there's CSS
	if ($('billboardname').value == "") {
		alert("<?php echo JText::_('BILLBOARD_MUST_HAVE_A_NAME', true); ?>");
	} else {
		submitform(pressbutton);
	}
}

// @TODO: should probably put this somewhere else
window.addEvent('domready', function() {
	var styling        = $('styling');
	var styling_table  = $('styling_table');
	var slider         = new Fx.Slide(styling_table).hide();

	styling.addEvent('click', function(e) {
			e = new Event(e);
			slider.toggle();
			e.stop();
	});
});
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_('BILLBOARD_CONTENT'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="billboardname"><?php echo JText::_('BILLBOARD_NAME'); ?>:</label></td>
						<td><input type="text" name="billboard[name]" id="billboardname" value="<?php echo htmlentities(stripslashes($this->row->name), ENT_QUOTES); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="billboardcollection"><?php echo JText::_('BILLBOARD_COLLECTION_NAME'); ?>:</label></td>
						<td><?php echo $this->clist; ?></td>
					</tr>
					<tr>
						<td class="key"><label for="ordering"><?php echo JText::_('BILLBOARD_ORDER'); ?>:</label></td>
						<td><?php echo $this->row->ordering; ?></td>
					</tr>
					<tr>
						<td class="key"><label for="billboardheader"><?php echo JText::_('BILLBOARD_HEADER'); ?>:</label></td>
						<td><input type="text" name="billboard[header]" id="billboardheader" value="<?php echo htmlentities(stripslashes($this->row->header), ENT_QUOTES); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="billboardbackgroundimg"><?php echo JText::_('BILLBOARD_BACKGROUND_IMG'); ?>:</label></td>
						<td><?php echo $this->image_location; ?><input type="text" name="billboard[background_img]" id="billboardbackgroundimg" value="<?php echo htmlentities(stripslashes($this->row->background_img), ENT_QUOTES); ?>" size="25" />
						<?php if (version_compare(JVERSION, '1.6', 'ge')) : ?>
							<a class="modal" href="<?php echo JRoute::_('index.php?option=com_media&view=images&tmpl=component&folder='.$this->media_path); ?>" rel="{handler: 'iframe', size: {x: 640, y: 520}}">Upload an image</a></td>
						<?php else : ?>
							<a class="modal" href="<?php echo JRoute::_('index.php?option=com_media&tmpl=component&task=popupUpload&folder='.$this->media_path); ?>" rel="{handler: 'iframe', size: {x: 640, y: 520}}">Upload an image</a></td>
						<?php endif; ?>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="billboard[text]"><?php echo JText::_('BILLBOARD_TEXT'); ?>:</label></td>
						<td>
							<?php
								$editorText = &JEditor::getInstance();
								echo $editorText->display('billboard[text]', htmlentities(stripslashes($this->row->text), ENT_QUOTES), '95%', '100px', '45', '13', false);
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
		<legend><?php echo JText::_('BILLBOARD_LEARN_MORE'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="billboardlearnmoretext"><?php echo JText::_('BILLBOARD_LEARN_MORE_TEXT'); ?>:</label></td>
						<td><input type="text" name="billboard[learn_more_text]" id="billboardlearnmoretext" value="<?php echo htmlentities(stripslashes($this->row->learn_more_text), ENT_QUOTES); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="billboardlearnmoretarget"><?php echo JText::_('BILLBOARD_LEARN_MORE_TARGET'); ?>:</label></td>
						<td><input type="text" name="billboard[learn_more_target]" id="billboardlearnmoretarget" value="<?php echo htmlentities(stripslashes($this->row->learn_more_target), ENT_QUOTES); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="billboardlearnmoreclass"><?php echo JText::_('BILLBOARD_LEARN_MORE_CLASS'); ?>:</label></td>
						<td><input type="text" name="billboard[learn_more_class]" id="billboardlearnmoreclass" value="<?php echo htmlentities(stripslashes($this->row->learn_more_class), ENT_QUOTES); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="billboardlearnmorelocation"><?php echo JText::_('BILLBOARD_LEARN_MORE_LOCATION'); ?>:</label></td>
						<td><?php echo $this->learnmorelocation; ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
		<!-- @TODO: remove inline styles -->
		<legend id="styling" style="cursor:pointer;"><?php echo JText::_('BILLBOARD_STYLING'); ?></legend>
			<br style="clear:both;" />
			<div id="styling_table">
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="billboardalias"><?php echo JText::_('BILLBOARD_ALIAS'); ?>:</label></td>
							<td><input type="text" name="billboard[alias]" id="billboardalias" value="<?php echo htmlentities(stripslashes($this->row->alias), ENT_QUOTES); ?>" size="50" /></td>
						</tr>
						<tr>
							<td class="key"><label for="billboardpadding"><?php echo JText::_('BILLBOARD_PADDING'); ?>:</label></td>
							<td><input type="text" name="billboard[padding]" id="billboardpadding" value="<?php echo htmlentities(stripslashes($this->row->padding), ENT_QUOTES); ?>" size="50" /></td>
						</tr>
						<tr>
							<td class="key" valign="top"><label for="billboard[css]"><?php echo JText::_('BILLBOARD_CSS'); ?>:</label></td>
							<td>
								<?php
									$editorCSS = &JEditor::getInstance();
									echo $editorCSS->display('billboard[css]', htmlentities(stripslashes($this->row->css), ENT_QUOTES), '95%', '100px', '45', '13', false);
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="billboard[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
