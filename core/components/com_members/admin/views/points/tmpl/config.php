<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title( Lang::txt( 'MEMBERS' ).': Manage Points', 'user.png' );
Toolbar::save( 'saveconfig', 'Save Configuration' );
Toolbar::cancel();

?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">Points</th>
				<th scope="col">Alias</th>
				<th scope="col">Description</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$rows = 50;
		$i = 0;
		for ($r = 0; $r < $rows; $r++)
		{
			?>
			<tr>
				<th scope="row">(<?php echo ($i + 1); ?>)</th>
				<td><input type="text" name="points[<?php echo $i; ?>]" value="<?php echo @$this->params[$i]->points; ?>" size="10" maxlength="10" /></td>
				<td><input type="text" name="alias[<?php echo $i; ?>]" value="<?php echo $this->escape(@$this->params[$i]->alias); ?>" size="20" maxlength="50" /></td>
				<td><input type="text" name="description[<?php echo $i; ?>]" value="<?php echo $this->escape(@$this->params[$i]->description); ?>" size="100" maxlength="255" /></td>
			</tr>
			<?php
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>