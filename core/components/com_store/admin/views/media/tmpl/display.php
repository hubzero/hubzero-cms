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

// No direct access.
defined('_HZEXEC_') or die();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
	<table class="formed">
		<thead>
			<tr>
				<th><label for="image"><?php echo Lang::txt('COM_STORE_WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="tmpl" value="component" />
					<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
					<input type="hidden" name="task" value="upload" />

					<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
					<input type="submit" value="<?php echo Lang::txt('COM_STORE_UPLOAD'); ?>" />
				</td>
			</tr>
		</tbody>
	</table>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

	<table class="formed">
		<thead>
			<tr>
				<th colspan="4"><label for="image"><?php echo Lang::txt('COM_STORE_PICTURE'); ?></label></th>
			</tr>
		</thead>
		<tbody>
<?php
	$k = 0;

	if ($this->file && file_exists(PATH_APP . $this->path . DS . $this->file))
	{
		$this_size = filesize(PATH_APP . $this->path . DS . $this->file);
		list($width, $height, $type, $attr) = getimagesize(PATH_APP . $this->path . DS . $this->file);
?>
			<tr>
				<td rowspan="6">
					<img src="<?php echo DS . trim(substr(PATH_APP, strlen(PATH_ROOT)), DS) . $this->path . DS . $this->file; ?>" alt="<?php echo Lang::txt('COM_STORE_PICTURE'); ?>" id="conimage" width="100" />
				</td>
				<td><?php echo Lang::txt('COM_STORE_FILE'); ?>:</td>
				<td><?php echo $this->file; ?></td>
			</tr>
			<tr>
				<td><?php echo Lang::txt('COM_STORE_SIZE'); ?>:</td>
				<td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
			</tr>
			<tr>
				<td><?php echo Lang::txt('COM_STORE_WIDTH'); ?>:</td>
				<td><?php echo $width; ?> px</td>
			</tr>
			<tr>
				<td><?php echo Lang::txt('COM_STORE_HEIGHT'); ?>:</td>
				<td><?php echo $height; ?> px</td>
			</tr>
			<tr>
				<td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=delete&current=' . $this->file . '&id=' . $this->id . '&' . Session::getFormToken() . '=1'); ?>">[ <?php echo Lang::txt('COM_STORE_DELETE'); ?> ]</a></td>
			</tr>
<?php } else { ?>
			<tr>
				<td colspan="4">
					<img src="<?php echo '../core/components/' . $this->option . '/site/assets/img/nophoto.gif'; ?>" alt="<?php echo Lang::txt('COM_STORE_NO_PICTURE'); ?>" />
					<input type="hidden" name="currentfile" value="" />
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
	<?php echo Html::input('token'); ?>
</form>
