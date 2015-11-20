<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="media">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<table class="formed">
			<thead>
				<tr>
					<th><label for="image"><?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?> <?php echo Lang::txt('COM_MEMBERS_MEDIA_WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
						<input type="hidden" name="task" value="upload" />

						<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?>" />
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
					<th colspan="4"><label for="image"><?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?></label></th>
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
					<td rowspan="6"><img src="<?php echo $this->profile->getPicture(0, false); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?>" id="conimage" /></td>
					<td><?php echo Lang::txt('COM_MEMBERS_MEDIA_FILE'); ?>:</td>
					<td><?php echo $this->file; ?></td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('COM_MEMBERS_MEDIA_SIZE'); ?>:</td>
					<td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('COM_MEMBERS_MEDIA_WIDTH'); ?>:</td>
					<td><?php echo $width; ?> px</td>
				</tr>
				<tr>
					<td><?php echo Lang::txt('COM_MEMBERS_MEDIA_HEIGHT'); ?>:</td>
					<td><?php echo $height; ?> px</td>
				</tr>
				<tr>
					<td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=remove&file=' . $this->file . '&id=' . $this->id . '&' . Session::getFormToken() . '=1'); ?>">[ <?php echo Lang::txt('JACTION_DELETE'); ?> ]</a></td>
				</tr>
			<?php } else { ?>
				<tr>
					<td colspan="4">
						<img src="<?php echo '..' . $this->config->get('defaultpic', '/core/components/com_members/site/assets/img/profile.gif'); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_NO_PICTURE'); ?>" />
						<input type="hidden" name="currentfile" value="" />
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php echo Html::input('token'); ?>
	</form>
</div>