<?php
// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Partners\Helpers\Permissions::getActions('partner_types');

// Toolbar is a helper class to simplify the creation of Toolbar 
// titles, buttons, spacers and dividers in the Admin Interface.
//
// Here we'll had the title of the component and options
// for saving based on if the user has permission to
// perform such actions. Everyone gets a cancel button.
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_PARTNERS_PARTNER_TYPES') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('partner_types');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if ($('#field-name').val() == ''){
		alert("<?php echo Lang::txt('COM_PARTNERS_ERROR_MISSING_NAME'); ?>");
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>
			<!--to access values from database, need to use $this->escape($this->row->get('variable name in database')) -->
			
			<div class="input-wrap">
				<label for="field-internal"><?php echo Lang::txt('COM_PARTNERS_FIELD_INTERNAL_NAME'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>	
				<input type="text" name="fields[internal]" id="field-internal" size="35" value="<?php echo $this->escape($this->row->get('internal')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-external"><?php echo Lang::txt('COM_PARTNERS_FIELD_EXTERNAL_NAME'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[external]" id="field-external" size="35" value="<?php echo $this->escape($this->row->get('external')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo Lang::txt('COM_PARTNERS_DESCRIPTION'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php echo $this->editor('fields[description]', $this->escape($this->row->get('description')), 50, 15, 'field-about', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
			</div>
		</fieldset>
	</div>


	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_PARTNERS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id', 0); ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
					</td>
				
			</tbody>
		</table>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>