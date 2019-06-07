<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div id="media">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<table class="formed">
			<thead>
				<tr>
					<th><label for="image"><?php echo Lang::txt('COM_TOOLS_UPLOAD'); ?> <?php echo Lang::txt('COM_TOOLS_WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->zone->get('id'); ?>" />
						<input type="hidden" name="task" value="upload" />

						<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_UPLOAD'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			if ($this->getError())
			{
				echo '<p class="error">' . $this->getError() . '</p>';
			}
		?>
		<table class="formed">
			<thead>
				<tr>
					<th colspan="4"><label for="image"><?php echo Lang::txt('COM_TOOLS_FIELDSET_IMAGE'); ?></label></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;

			$path = $this->zone->logo('path');
			$file = $this->zone->get('picture');

			if ($file && file_exists($path . DS . $file))
			{
				$this_size = filesize($path . DS . $file);
				list($width, $height, $type, $attr) = getimagesize($path . DS . $file);
				?>
				<tr>
					<td rowspan="6">
						<img src="<?php echo substr($path, strlen(PATH_ROOT)) . '/' . $file; ?>" alt="<?php echo Lang::txt('COM_TOOLS_FIELDSET_IMAGE'); ?>" id="conimage" />
					</td>
					<th><?php echo Lang::txt('COM_TOOLS_IMAGE_FILE'); ?>:</th>
					<td><?php echo $file; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_TOOLS_IMAGE_SIZE'); ?>:</th>
					<td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_TOOLS_IMAGE_WIDTH'); ?>:</th>
					<td><?php echo $width; ?> px</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_TOOLS_IMAGE_HEIGHT'); ?>:</th>
					<td><?php echo $height; ?> px</td>
				</tr>
				<tr>
					<td><input type="hidden" name="currentfile" value="<?php echo $file; ?>" /></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=removefile&id=' . $this->zone->get('id') . '&' . Session::getFormToken() . '=1'); ?>">[ <?php echo Lang::txt('JDELETE'); ?> ]</a></td>
				</tr>
			<?php } else { ?>
				<tr>
					<td colspan="4">
						<?php echo Lang::txt('COM_TOOLS_IMAGE_NONE'); ?>
						<input type="hidden" name="currentfile" value="" />
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php echo Html::input('token'); ?>
	</form>
</div>