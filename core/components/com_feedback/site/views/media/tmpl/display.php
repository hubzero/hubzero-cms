<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js('media.js');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<table>
		<tbody>
			<?php
			$k = 0;

			if ($this->file && file_exists($this->file_path . DS . $this->file))
			{
				$this_size = filesize($this->file_path . DS . $this->file);
				list($ow, $oh, $type, $attr) = getimagesize($this->file_path . DS . $this->file);

				// scale if image is bigger than 120w x120h
				$num = max($ow/120, $oh/120);
				if ($num > 1)
				{
					$mw = round($ow/$num);
					$mh = round($oh/$num);
				}
				else
				{
					$mw = $ow;
					$mh = $oh;
				}
				?>
				<tr>
					<td>
						<img src="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" alt="" id="conimage" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" />
					</td>
					<td width="100%">
						<input type="hidden" name="conimg" value="<?php echo $this->escape($this->webpath . DS . $this->path . DS . $this->file); ?>" />
						<input type="hidden" name="task" value="delete" />
						<input type="hidden" name="file" id="file" value="<?php echo $this->escape($this->file); ?>" />
						<input type="submit" name="submit" value="<?php echo Lang::txt('JACTION_DELETE'); ?>" />
					</td>
				</tr>
			<?php } else { ?>
				<tr>
					<td>
						<img src="<?php echo $this->default_picture; ?>" alt="" id="oimage" name="oimage" />
					</td>
					<td>
						<p><?php echo Lang::txt('COM_FEEDBACK_STORY_ADD_PICTURE'); ?><br /><small>(gif/jpg/jpeg/png - 200K max)</small></p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="hidden" name="conimg" value="" />
						<input type="hidden" name="task" value="upload" />
						<input type="hidden" name="currentfile" value="<?php echo $this->escape($this->file); ?>" />
						<input type="file" name="upload" id="upload" size="10" /> <input type="submit" value="<?php echo Lang::txt('COM_FEEDBACK_UPLOAD'); ?>" />
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
</form>