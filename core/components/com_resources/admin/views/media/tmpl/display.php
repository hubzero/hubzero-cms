<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->getError())
{
	echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}
$this->js('media.js');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<p><?php echo Lang::txt('COM_RESOURCES_MEDIA_PATH', str_replace(PATH_ROOT, 'ROOT', $this->path)); ?></p>

	<fieldset>
		<label>
			<?php echo Lang::txt('COM_RESOURCES_MEDIA_DIRECTORY'); ?>
			<?php echo $this->dirPath; ?>
		</label>

		<div id="themanager" class="manager">
			<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=list&tmpl=component&listdir=' . $this->listdir . '&subdir=' . $this->subdir); ?>" name="imgManager" id="imgManager" width="98%" height="180"
				data-dir="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&task=list&tmpl=component'); ?>"></iframe>
		</div>
	</fieldset>

	<fieldset>
		<table>
			<tbody>
				<tr>
					<td><label for="upload"><?php echo Lang::txt('COM_RESOURCES_MEDIA_UPLOAD'); ?></label></td>
					<td><input type="file" name="upload" id="upload" /></td>
				</tr>
				<tr>
					<td> </td>
					<td><input type="checkbox" name="batch" id="batch" value="1" /> <label for="batch"><?php echo Lang::txt('COM_RESOURCES_MEDIA_UPLOAD_UNPACK'); ?></label></td>
				</tr>
				<tr>
					<td><label for="foldername"><?php echo Lang::txt('COM_RESOURCES_MEDIA_CREATE_DIRECTORY'); ?></label></td>
					<td><input type="text" name="foldername" id="foldername" /></td>
				</tr>
				<tr>
					<td> </td>
					<td><input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_MEDIA_ACTION_UPLOAD'); ?>" /></td>
				</tr>
			</tbody>
		</table>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>
