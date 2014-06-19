<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_TOOLS').': '. $text, 'tools.png');
JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('tool');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('item-form');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_TOOLS_FIELD_TITLE'); ?>:</label><br />
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_FIELD_ID'); ?>:</th>
					<td><?php echo $this->escape($this->row->id);?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_FIELD_NAME'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->row->toolname)); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>