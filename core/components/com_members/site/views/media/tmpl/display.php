<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="member-picture">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media'); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<fieldset>
			<legend><?php echo Lang::txt('UPLOAD'); ?> <?php echo Lang::txt('WILL_REPLACE_EXISTING_IMAGE'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="media" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
			<?php echo Html::input('token'); ?>

			<input type="file" name="upload" id="upload" size="17" />
			<input type="submit" value="<?php echo Lang::txt('UPLOAD'); ?>" />
		</fieldset>

		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<table>
			<caption><label for="image"><?php echo Lang::txt('MEMBER_PICTURE'); ?></label></caption>
			<tbody>
			<?php
			$k = 0;

			if ($this->file && file_exists($this->file_path . DS . $this->file))
			{
				$this_size = filesize($this->file_path . DS . $this->file);
				list($width, $height, $type, $attr) = getimagesize($this->file_path . DS . $this->file);
				?>
				<tr>
					<td rowspan="6"><img src="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" alt="<?php echo Lang::txt('MEMBER_PICTURE'); ?>" id="conimage" /></td>
					<td><?php echo Lang::txt('FILE'); ?>:</td>
					<td><?php echo $this->file; ?></td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('SIZE'); ?>:</td>
					<td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('WIDTH'); ?>:</td>
					<td><?php echo $width; ?> px</td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('HEIGHT'); ?>:</td>
					<td><?php echo $height; ?> px</td>
				</tr>
				<tr>
					<td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
					<td><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=deleteimg&amp;file=<?php echo $this->file; ?>&amp;id=<?php echo $this->id; ?>&amp;no_html=1">[ <?php echo Lang::txt('JACTION_DELETE'); ?> ]</a></td>
				</tr>
			<?php } else { ?>
				<tr>
					<td colspan="4">
						<img src="<?php echo $this->default_picture; ?>" alt="<?php echo Lang::txt('NO_MEMBER_PICTURE'); ?>" />
						<input type="hidden" name="currentfile" value="" />
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</form>
</div>
