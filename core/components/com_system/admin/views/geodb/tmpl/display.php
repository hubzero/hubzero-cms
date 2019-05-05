<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SYSTEM_GEO_CONFIGURATION'), 'config');
Toolbar::preferences($this->option, '550');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_SYSTEM_GEO_HUBCONFIG'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><input type="submit" name="importHubConfig" id="importHubConfig" value="<?php echo Lang::txt('COM_SYSTEM_GEO_IMPORT'); ?>" /></td>
							<td><?php echo Lang::txt('COM_SYSTEM_GEO_IMPORT_HUBCONFIG'); ?></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<p>
				<?php echo Lang::txt('COM_SYSTEM_GEO_HELP'); ?>
			</p>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="importHubConfig" />
</form>
