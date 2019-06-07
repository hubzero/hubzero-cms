<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="media">
	<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<table class="formed">
			<thead>
				<tr>
					<th><label for="image"><?php echo Lang::txt('COM_COURSES_UPLOAD'); ?> <?php echo Lang::txt('WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
						<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
						<input type="hidden" name="task" value="upload" />

						<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php echo Lang::txt('COM_COURSES_UPLOAD'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			if ($this->getError())
			{
				echo $this->getError();
			}
		?>
		<table class="formed">
			<thead>
				<tr>
					<th colspan="4"><label for="image"><?php echo Lang::txt('COM_COURSES_LOGO'); ?></label></th>
				</tr>
			</thead>
			<tbody>
<?php
	$k = 0;

	if ($this->file && file_exists($this->path . DS . $this->file))
	{
		$this_size = filesize($this->path . DS . $this->file);
		list($width, $height, $type, $attr) = getimagesize($this->path . DS . $this->file);
?>
				<tr>
					<td rowspan="6"><img src="<?php echo rtrim(Request::root(true), '/') . substr($this->path, strlen(PATH_ROOT)) . DS . $this->file; ?>" alt="<?php echo Lang::txt('COM_COURSES_LOGO'); ?>" id="conimage" /></td>
					<td><?php echo Lang::txt('COM_COURSES_FILE'); ?>:</td>
					<td><?php echo $this->file; ?></td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('COM_COURSES_PICTURE_SIZE'); ?>:</td>
					<td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('COM_COURSES_PICTURE_WIDTH'); ?>:</td>
					<td><?php echo $width; ?> px</td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('COM_COURSES_PICTURE_HEIGHT'); ?>:</td>
					<td><?php echo $height; ?> px</td>
				</tr>
				<tr>
					<td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&tmpl=component&task=remove&type=' . $this->type . '&file=' . $this->file . '&id=' . $this->id . '&' . Session::getFormToken() . '=1'); ?>">[ <?php echo Lang::txt('COM_COURSES_DELETE'); ?> ]</a></td>
				</tr>
<?php } else { ?>
				<tr>
					<td colspan="4">
						<?php echo Lang::txt('COM_COURSES_LOGO_NONE'); ?>
						<input type="hidden" name="currentfile" value="" />
					</td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		<?php echo Html::input('token'); ?>
	</form>
</div>