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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('media.css');
?>
<script type="text/javascript">
	function updateDir()
	{
		var allPaths = window.top.document.forms[0].dirPath.options;
		for (i=0; i<allPaths.length; i++) {
			allPaths.item(i).selected = false;
			if (allPaths.item(i).value == '<?php echo $this->group->get('gidNumber'); ?>') {
				allPaths.item(i).selected = true;
			}
		}
	}
	function deleteFile(file)
	{
		if (confirm('Delete file "' + file + '"?')) {
			return true;
		}
		return false;
	}
	function deleteFolder(folder, numFiles)
	{
		if (numFiles > 0) {
			alert('There are ' + numFiles + ' files/folders in "' + folder + '". Please delete all files/folder in "' + folder + '" first.');
			return false;
		}
		if (confirm('Delete folder "' + folder + '"?')) {
			return true;
		}
		return false;
	}
</script>
<div id="attachments">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gidNumber=' . $this->group->get('gidNumber')); ?>" method="post" id="filelist">
		<?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
			<p><?php echo Lang::txt('COM_GROUPS_NO_FILES_FOUND'); ?></p>
		<?php } else { ?>
			<table>
				<tbody>
				<?php
				foreach ($this->folders as $k => $folder)
				{
					$num_files = count(Filesystem::files($k));
					$k = substr($k, strlen($this->path));
					?>
					<tr>
						<td width="100%">
							<a class="icon-folder folder" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&dir=' . urlencode($k) . '&gidNumber=' . $this->group->get('gidNumber') . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" target="media">
								<?php echo trim($k, DS); ?>
							</a>
						</td>
						<td>
							<a class="icon-delete delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deletefolder&dir=' . urlencode($this->dir) . '&folder=' . urlencode($folder) . '&gidNumber=' . $this->group->get('gidNumber') . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" target="media" onclick="return deleteFolder('<?php echo basename($folder); ?>', '<?php echo $num_files; ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
							</a>
						</td>
					</tr>
				<?php } ?>
				<?php foreach ($this->docs as $k => $doc) { ?>
					<tr>
						<td width="100%">
							<a download target="_blank" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gidNumber=' . $this->group->get('gidNumber') . '&task=download&file=' . urlencode(substr($k, strlen(PATH_ROOT))) . '&' . Session::getFormToken() . '=1'); ?>" class="icon-file file <?php echo Filesystem::extension($doc); ?>">
								<?php
								$k = substr($k, strlen($this->path));
								echo $this->escape(trim($k, DS)); ?>
							</a>
						</td>
						<td>
							<a class="icon-delete delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deletefile&file=' . urlencode($k) . '&gidNumber=' . $this->group->get('gidNumber') . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" target="media" onclick="return deleteFile('<?php echo basename($doc); ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
							</a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } ?>

		<?php echo Html::input('token'); ?>
	</form>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
</div>