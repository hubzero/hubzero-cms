<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DIRECTORY'); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_STATUS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->directory as $dir => $info): ?>
				<tr>
					<td>
						<?php echo Html::directory('message', $dir, $info['message']);?>
					</td>
					<td>
						<?php echo Html::directory('writable', $info['writable']);?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</fieldset>
