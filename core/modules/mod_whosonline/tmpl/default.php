<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>

<div class="<?php echo $this->params->get('moduleclass_sfx', ''); ?>">
	<?php if ($this->params->get('showmode', 0) == 0 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN'); ?></th>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_GUESTS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo number_format($this->loggedInCount); ?></td>
					<td><?php echo number_format($this->guestCount); ?></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if ($this->params->get('showmode', 0) == 1 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th colspan="2"><?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN_NAME'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->loggedInList as $loggedin) : ?>
					<tr>
						<td><?php echo $loggedin->get('name'); ?></td>
						<td>
							<a href="<?php echo Route::url('index.php?option=com_members&id=' . $loggedin->get('id')); ?>">
								<?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN_VIEW_PROFILE'); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<table>
		<tbody>
			<tr>
				<td>
					<a class="btn btn-secondary opposite icon-next" href="<?php echo Route::url('index.php?option=com_members&task=activity'); ?>">
						<?php echo Lang::txt('MOD_WHOSONLINE_VIEW_ALL_ACTIVITIY'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>