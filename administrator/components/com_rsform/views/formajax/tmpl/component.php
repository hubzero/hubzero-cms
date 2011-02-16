<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table class="componentForm" border="0" cellspacing="0" cellpadding="0">
	<?php $i = 0; ?>
	<?php foreach ($this->fields as $field) { ?>
		<?php if ($i == 2 && $this->show_save) { ?>
		<tr>
			<td><input type="button" name="componentSaveButton" onclick="processComponent('<?php echo $this->type_id; ?>')" value="<?php echo $this->componentId ? JText::_('Update') : JText::_('Save'); ?>" style="float:right; margin-right:20px;" /></td>
		</tr>
		<?php } ?>
		<tr id="id<?php echo $field->name; ?>">
			<td><?php echo $field->body; ?></td>
		</tr>
	<?php $i++; ?>
	<?php } ?>
	<tr>
		<td><input type="button" name="componentSaveButton" onclick="processComponent('<?php echo $this->type_id; ?>')" value="<?php echo $this->componentId ? JText::_('Update') : JText::_('Save'); ?>" style="float:right; margin-right:20px;" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>