<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS_IMPORT'), 'user');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<div id="item-form">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="grid">
		<div class="col span8">
			<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_LEGEND'); ?></span></legend>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="processImport" />

					<div class="input-wrap">
						<label for="conf_text"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CONF_TEXT'); ?>:</label>
						<p class="info conf-text-note"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CONF_TEXT_NOTE'); ?></p>
						<textarea name="conf_text" id="conf_text" cols="30" rows="10"></textarea>
					</div>
					<div class="input-wrap">
						<label for="overwrite_existing"><?php echo Lang::txt('COM_MEMBERS_QUOTA_OVERWRITE_EXISTING'); ?></label>
						<input type="checkbox" name="overwrite_existing" id="overwrite_existing" value="1" />
					</div>
					<p class="submit-button">
						<input class="btn btn-primary" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
					</p>
				</fieldset>
				<?php echo Html::input('token'); ?>
			</form>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_MISSING_USERS'); ?>:</th>
						<td>
							<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
								<input type="hidden" name="task" value="importMissing" />
								<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
							</form>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p>
								<?php echo Lang::txt('COM_MEMBERS_QUOTA_MISSING_USERS_IMPORT_DESCRIPTION'); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>