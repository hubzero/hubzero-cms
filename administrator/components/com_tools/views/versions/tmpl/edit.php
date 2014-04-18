<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('COM_TOOLS').': '. $text, 'tools.png');
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('item-form');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('TOOL_VERSION_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-command"><?php echo JText::_('TOOL_COMMAND'); ?>:</label><br />
				<input type="text" name="fields[vnc_command]" id="field-command" value="<?php echo $this->escape(stripslashes($this->row->vnc_command));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-timeout"><?php echo JText::_('TOOL_TIMEOUT'); ?>:</label><br />
				<input type="text" name="fields[vnc_timeout]" id="field-timeout" value="<?php echo $this->escape(stripslashes($this->row->vnc_timeout));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-hostreq"><?php echo JText::_('TOOL_HOSTREQ'); ?>:</label><br />
				<input type="text" name="fields[hostreq]" id="field-hostreq" value="<?php echo $this->escape(stripslashes(implode(', ', $this->row->hostreq)));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-mw"><?php echo JText::_('TOOL_MIDDLEWARE'); ?>:</label><br />
				<input type="text" name="fields[mw]" id="field-mw" value="<?php echo $this->escape(stripslashes($this->row->mw));?>" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-params"><?php echo JText::_('TOOL_PARAMS'); ?>:</label><br />
				<textarea name="fields[params]" id="field-params" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->params));?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('TOOL_TITLE'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->parent->title));?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('TOOL_TOOLNAME'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->parent->toolname));?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('TOOL_VERSON'); ?>:</th>
					<td><?php echo $this->escape($this->row->id);?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->parent->id; ?>" />
	<input type="hidden" name="fields[version]" value="<?php echo $this->row->id; ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>