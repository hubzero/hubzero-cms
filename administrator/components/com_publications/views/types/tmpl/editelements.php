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

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATION') . ' ' . JText::_('COM_PUBLICATIONS_MASTER_TYPE') . ' - ' . $this->row->type . ': [ ' . JText::_('COM_PUBLICATIONS_EDIT_BLOCK_ELEMENTS') . ' ]', 'addedit.png');
JToolBarHelper::save('saveelements');
JToolBarHelper::cancel();

$params = new JRegistry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks	   = $manifest->blocks;
$sequence  = $this->sequence;
$block	   = $blocks->$sequence;
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
	return;
}
</script>
<p class="backto"><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=types&amp;task=edit&amp;id[]=<?php echo $this->row->id; ?>"><?php echo JText::_('COM_PUBLICATIONS_MTYPE_BACK') . ' ' . $this->row->type . ' ' . JText::_('COM_PUBLICATIONS_MASTER_TYPE'); ?></a></p>
<form action="index.php" method="post" id="item-form" name="adminForm">
		<fieldset class="adminform">
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="bid" value="<?php echo $this->sequence; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="saveelements" />
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_EDIT_BLOCK_ELEMENTS'); ?></span></legend>
			<p class="warning"><?php echo JText::_('COM_PUBLICATIONS_EDIT_BLOCK_ELEMENTS_WARNING'); ?></p>
			<?php foreach ($block->elements as $elementId => $element) { ?>
			<fieldset class="adminform">
				<legend><span class="block-sequence"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ID') . ': ' . $elementId; ?> - <?php echo $element->name; ?> - <?php echo $element->name == 'metadata' ? $element->params->input : $element->params->type; ?></span></legend>
				<div class="input-wrap">
					<label for="field-el-<?php echo $elementId; ?>-label"><?php echo JText::_('COM_PUBLICATIONS_FIELD_LABEL'); ?>:</label>
					<input type="text" name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][label]" id="field-el-<?php echo $elementId; ?>-label" maxlength="255" value="<?php echo $element->label;  ?>" />
				</div>
				<div class="input-wrap"><label for="field-el-<?php echo $elementId; ?>-about"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ELEMENT_ABOUT'); ?>:</label><textarea name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][about]" id="field-el-<?php echo $elementId; ?>-about"><?php echo htmlspecialchars($element->about); ?></textarea></div>
				<div class="input-wrap">
					<label for="field-el-<?php echo $elementId; ?>-adminTips"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ELEMENT_ADMIN_TIPS'); ?>:</label><textarea name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][adminTips]" id="field-el-<?php echo $elementId; ?>-adminTips"><?php echo htmlspecialchars($element->adminTips); ?></textarea>
				</div>
				<?php foreach ($element->params as $paramname => $paramvalue) { ?>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($paramname)); ?></label>
					<?php
						if ($element->type == 'attachment' && $paramname == 'type') {
						?>
						<select name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][params][<?php echo $paramname; ?>]">
							<option value="file" <?php echo $paramvalue == 'file' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPE_FILE'); ?></option>
							<option value="link" <?php echo $paramvalue == 'link' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPE_LINK'); ?></option>
						</select>
						<?php }
						elseif ($paramname == 'required') {
						?>
						<select name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][params][<?php echo $paramname; ?>]">
							<option value="1" <?php echo $paramvalue == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('JYES'); ?></option>
							<option value="0" <?php echo $paramvalue == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNO'); ?></option>
						</select>
						<?php }
						elseif ($element->type == 'attachment' && $paramname == 'role')
						{ ?>
						<select name="curation[blocks][elements][<?php echo $elementId; ?>][<?php echo $sequence; ?>][params][<?php echo $paramname; ?>]">
							<option value="1" <?php echo $paramvalue == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_ROLE_PRIMARY'); ?></option>
							<option value="2" <?php echo ($paramvalue == 2 || $paramvalue == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_ROLE_SUPPORTING'); ?></option>
							<option value="3" <?php echo $paramvalue == 3 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_ROLE_GALLERY'); ?></option>
						</select>
						<?php }
						elseif ($paramname == 'typeParams') {
							foreach ($paramvalue as $tpName => $tpValue) { ?>
							<div class="input-wrap">
								<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($tpName)); ?></label>
							<?php
								if ($tpName == 'handler') { ?>
								<select name="curation[blocks][elements][<?php echo $elementId; ?>][<?php echo $sequence; ?>][params][<?php echo $paramname; ?>][<?php echo $tpName; ?>]">
									<option value="" <?php echo !$tpValue ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNONE'); ?></option>
									<option value="imageviewer" <?php echo $tpValue == 'imageviewer' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_HANDLER_IMAGE'); ?></option>
								</select>
							<?php }
								elseif (is_array($tpValue)) {
								$tpVal = implode(',', $tpValue); ?>
								<input type="text" name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][params][<?php echo $paramname; ?>][<?php echo $tpName; ?>]" value="<?php echo $tpVal;  ?>" />
							<?php	}
							else {
								?>
							<input type="text" name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][params][<?php echo $paramname; ?>][<?php echo $tpName; ?>]" value="<?php echo $tpValue;  ?>" />
							<?php } ?>
							</div>
						<?php }
						}
						elseif (is_array($paramvalue)) {
						$val = implode(',', $paramvalue);
					?>
					<input type="text" name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][params][<?php echo $paramname; ?>]" value="<?php echo $val;  ?>" />
					<?php } else { ?>
						<input type="text" name="curation[blocks][<?php echo $sequence; ?>][elements][<?php echo $elementId; ?>][params][<?php echo $paramname; ?>]" value="<?php echo $paramvalue;  ?>" />
					<?php } ?>
				</div>
				<?php } ?>
			</fieldset>
			<?php } ?>

		</fieldset>
	<?php echo JHTML::_('form.token'); ?>
</form>