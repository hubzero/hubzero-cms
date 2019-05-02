<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS'), 'tools');

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<caption><?php echo $this->hostname; ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_STATUS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
				<?php
				if ($this->output)
				{
					foreach ($this->output as $line)
					{
						echo "$line<br />\n";
					}
				}
				?>
				</td>
			</td>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>