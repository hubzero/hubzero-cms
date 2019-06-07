<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = (User::authorise('core.admin', $this->option) || User::authorise('core.edit', $this->option));
?>
<div id="hosts">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
		<?php if ($canDo) { ?>
			<table>
				<tbody>
					<tr>
						<td>
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
							<input type="hidden" name="tmpl" value="component" />
							<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
							<input type="hidden" name="task" value="add" />

							<input type="text" name="host" value="" />
							<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_HOSTS_ADD'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			<br />
		<?php } ?>

		<table class="paramlist admintable">
			<tbody>
				<?php
				if ($this->rows)
				{
					foreach ($this->rows as $row)
					{
						?>
						<tr>
							<td class="paramlist_key"><?php echo $row->get('host'); ?></td>
							<?php if ($canDo) { ?>
								<td class="paramlist_value"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=remove&host=' . $row->get('host') . '&id=' . $this->id . '&' . Session::getFormToken() . '=1'); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a></td>
							<?php } ?>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>

		<?php echo Html::input('token'); ?>
	</form>
</div>