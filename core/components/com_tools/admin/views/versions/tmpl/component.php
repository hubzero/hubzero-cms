<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('modal');

$this->js();
$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="4" class="align-right">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addZone&version=' . $this->version . '&tmpl=component'); ?>" class="button edit-asset" rel="{type: 'iframe', size: {x: 570, y: 550}}"><?php echo Lang::txt('COM_TOOLS_ADD_ZONE'); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_ZONE_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_PUBLISH_UP'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_PUBLISH_DOWN'); ?></th>
				<th scope="col">X</th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
	// Grab the zone name
	$zone = new \Components\Tools\Tables\Zones($mwdb);
	$zone->load($row->zone_id);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editZone&id=' . $row->id . '&tmpl=component'); ?>">
						<?php echo $this->escape(stripslashes($zone->title)); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->publish_up)); ?>
				</td>
				<td>
					<span><?php echo $this->escape(stripslashes($row->publish_down)); ?></span>
				</td>
				<td>
					<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=removeZone&id=' . $row->id . '&version=' . $this->version . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo Lang::txt('X'); ?></span>
					</a>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" id="task" value="edit" />

	<?php echo Html::input('token'); ?>
</form>