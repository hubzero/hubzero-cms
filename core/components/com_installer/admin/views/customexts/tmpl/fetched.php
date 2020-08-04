<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_' . $this->controller), 'customexts');
Toolbar::custom('default', 'back', 'back', 'COM_INSTALLER_CUSTOMEXTS_BACK', false);
Toolbar::spacer();
Toolbar::custom('doupdate', 'merge', '', 'COM_INSTALLER_CUSTOMEXTS_MERGE_CODE', false);

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<?php if (!empty($this->success)) : ?>
		<table class="adminlist success">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_SUCCESS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->success as $success) : ?>
					<tr>
						<td class="merge-success">
							<?php
								echo '<strong>Extension: ' . $success['extension'] . '</strong>';
								echo '<p>' . Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_SUCCESS_DESC') . '</p>';
							?>
							<hr />
							<code><?php echo implode('<br>', $success['message']); ?></code>

							<?php if ($success['message'][0] != Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_CODE_UP_TO_DATE')
										&& !preg_match('/ineligible/', $success['message'][0])) : ?>
								<label class="merge">
									<?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_MERGE'); ?>
									<input type="checkbox" name="id[]" checked="checked" value="<?php echo $success['ext_id']; ?>" />
								</label>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<br /><br />

	<?php if (!empty($this->failed)) : ?>
		<table class="adminlist failed">
			<thead>
			 	<tr>
					<th scope="col"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_FAIL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->failed as $failed) : ?>
					<tr>
						<td>
							<?php
								echo '<strong>Extension: ' . $success['extension'] . '</strong>';
							?>
							<br />
							<br />
							<pre><?php echo $failed['message']; ?></pre>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="doupdate" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>