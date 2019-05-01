<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$text = ($this->task == 'edit' ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW'));

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
	if ($canDo->get('core.edit'))
	{
		Toolbar::save();
	}
	Toolbar::cancel();
}

Html::behavior('framework');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.roleid.value == '') {
		alert('<?php echo Lang::txt('COM_GROUPS_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
	window.top.setTimeout("window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=membership&gid=<?php echo $this->group->get('cn'); ?>'", 700);
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="<?php echo ($tmpl == 'component') ? 'component' : 'item'; ?>-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="fltrt configuration-options">
				<button type="button" onclick="submitbutton('delegate');"><?php echo Lang::txt( 'COM_GROUPS_MEMBER_SAVE' );?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt( 'COM_GROUPS_MEMBER_CANCEL' );?></button>
			</div>
			<?php echo Lang::txt('COM_GROUPS_ROLE_ASSIGN') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col span12">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_GROUPS_DETAILS'); ?></span></legend>

			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
			<input type="hidden" name="task" value="delegate" />

			<?php
			foreach ($this->ids as $i => $id)
			{
				?>
				<input type="hidden" name="id[<?php echo $i; ?>]" value="<?php echo $id; ?>" />
				<?php
			}
			?>

			<div class="input-wrap">
				<label for="field-roleid"><?php echo Lang::txt('COM_GROUPS_ROLE_CHOOSE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<select name="roleid" id="field-roleid">
					<option value="0"><?php echo Lang::txt('COM_GROUPS_ROLE_SELECT'); ?></option>
					<?php foreach ($this->rows as $row) { ?>
						<option value="<?php echo $row->get('id'); ?>"><?php echo $this->escape($row->get('name')); ?></option>
					<?php } ?>
				</select>
			</div>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>
