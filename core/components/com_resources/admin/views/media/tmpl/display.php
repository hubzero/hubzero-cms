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

if ($this->getError()) {
	echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}
?>
<script type="text/javascript">
function dirup()
{
	var urlquery = frames['imgManager'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('listdir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['imgManager'].location.href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&task=list&tmpl=component&listdir=');?>" + listdir;
}

function goUpDir()
{
	var listdir = document.getElementById('listdir');
	var selection = document.forms[0].dirPath;
	var dir = selection.options[selection.selectedIndex].value;
	frames['imgManager'].location.href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&task=list&tmpl=component&listdir=');?>" + listdir.value + '&subdir=' + dir;
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<p><?php echo Lang::txt('COM_RESOURCES_MEDIA_PATH', $this->path); ?></p>

	<fieldset>
		<label>
			<?php echo Lang::txt('COM_RESOURCES_MEDIA_DIRECTORY'); ?>
			<?php echo $this->dirPath; ?>
		</label>

		<div id="themanager" class="manager">
			<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=list&tmpl=component&listdir=' . $this->listdir . '&subdir=' . $this->subdir); ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
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
