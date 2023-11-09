<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title(Lang::txt('COM_INSTALLER'), 'groups.png');
Toolbar::custom('display', 'back', 'back', 'COM_INSTALLER_CUSTOMEXTS_BACK', false);

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
		<table class="adminlist success">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_PULL_SUCCESS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->msg as $msg) : ?>
					<tr>
						<td>
							<?php
							echo '<strong> Extension:  ' . $msg['extension'] . '</strong>';
							?>
							<br />
							<br />
							<pre><?php echo $msg['message']; ?></pre>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<br /><br />

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>
