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

$canDo = StoreHelper::getActions('component');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';

JToolBarHelper::title(JText::_('COM_STORE_MANAGER') . $text, 'store.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('item');

$created = NULL;
if (intval($this->row->created) <> 0)
{
	$created = JHTML::_('date', $this->row->created, JText::_('COM_STORE_DATE_FORMAT_HZ1'));
}

?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
<?php //if (isset($this->row->id)) { ?>
			<legend><span><?php echo isset($this->row->id) ? JText::_('COM_STORE_STORE') . ' ' . JText::_('COM_STORE_ITEM') . ' #' . $this->row->id . ' ' . JText::_('COM_STORE_DETAILS') : JText::_('COM_STORE_NEW_ITEM'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-category"><?php echo JText::_('COM_STORE_CATEGORY'); ?>:</label><br />
				<select name="category" id="field-category">
					<option value="service"<?php if ($this->row->category == 'service') { echo ' selected="selected"'; } ?>>Service</option>
					<option value="wear"<?php if ($this->row->category == 'wear') { echo ' selected="selected"'; } ?>>Wear</option>
					<option value="office"<?php if ($this->row->category == 'office') { echo ' selected="selected"'; } ?>>Office</option>
					<option value="fun"<?php if ($this->row->category == 'fun') { echo ' selected="selected"'; } ?>>Fun</option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-price"><?php echo JText::_('COM_STORE_PRICE'); ?>:</label><br />
				<input type="text" name="price" id="field-price" value="<?php echo $this->escape(stripslashes($this->row->price)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_STORE_TITLE'); ?>:</label></td>
				<input type="text" name="title" id="field-title" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_STORE_WARNING_DESCR'); ?>">
				<label for="field-description"><?php echo JText::_('COM_STORE_DESCRIPTION'); ?>:</label><br />
				<textarea name="description" id="field-description" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
				<span class="hint"><?php echo JText::_('COM_STORE_WARNING_DESCR'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_STORE_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="published" id="field-published" value="1" <?php echo ($this->row->published) ? 'checked="checked"' : ''; ?> />
				<label for="field-published"><?php echo JText::_('COM_STORE_PUBLISHED'); ?></label>
			</div>
			<div class="input-wrap">
				<input type="checkbox" name="available" id="field-available" value="1" <?php echo ($this->row->available) ? 'checked="checked"' : ''; ?> />
				<label for="field-available"><?php echo ucfirst(JText::_('COM_STORE_INSTOCK')); ?></label>
			</div>
			<div class="input-wrap">
				<input type="checkbox" name="featured" id="field-featured" value="1" <?php echo ($this->row->featured) ? 'checked="checked"' : ''; ?> />
				<label for="field-featured"><?php echo JText::_('COM_STORE_FEATURED'); ?></label>
			</div>
			<div class="input-wrap">
				<label for="field-sizes"><?php echo JText::_('COM_STORE_AV_SIZES'); ?>:</label><br />
				<input type="text" name="sizes" id="field-sizes" size="15" value="<?php echo (isset($this->row->size)) ? $this->escape(stripslashes($this->row->size)) : '' ; ?>" /><br /><?php echo JText::_('COM_STORE_SAMPLE_SIZES'); ?>:
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_STORE_PICTURE'); ?></span></legend>
<?php
	if ($this->row->id != 0) {
?>
			<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;id=<?php echo $this->row->id; ?>"></iframe>
<?php
	} else {
		echo '<p class="alert">' . JText::_('COM_STORE_MUST_BE_SAVED_BEFORE_PICTURE') . '</p>';
	}
?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
<?php // } // end if id exists ?>

	<?php echo JHTML::_('form.token'); ?>
</form>
