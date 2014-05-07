<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('Edit Host') : JText::_('New Host'));

JToolBarHelper::title(JText::_( 'Tools' ).': '. $text, 'tools.png');
JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::help('hosttype');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Details'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-name"><?php echo JText::_('Name'); ?>:</label><br />
				<input type="text" name="fields[name]" id="field-name" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-value"><?php echo JText::_('Value'); ?>:</label><br />
				<input type="text" name="fields[value]" id="field-value" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->value)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('Description'); ?>:</label><br />
				<input type="text" name="fields[description]" id="field-description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('Bit#'); ?></th>
					<td><?php echo $this->escape($this->bit); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('References'); ?></th>
					<td><?php echo $this->escape($this->refs); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[status]" value="check" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->name); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>