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

// No direct access.
defined('_HZEXEC_') or die();
?>
<script type="text/javascript">
	function updateDir()
	{
		var allPaths = window.top.document.forms[0].dirPath.options;
		for (i=0; i<allPaths.length; i++)
		{
			allPaths.item(i).selected = false;
			if ((allPaths.item(i).value)== '<?php if (strlen($this->listdir)>0) { echo $this->listdir ;} else { echo '/';}  ?>') {
				allPaths.item(i).selected = true;
			}
		}
	}
	function deleteFile(file)
	{
		if (confirm("Delete file \""+file+"\"?")) {
			return true;
		}

		return false;
	}
	function deleteFolder(folder, numFiles)
	{
		if (numFiles > 0) {
			alert('<?php echo Lang::txt('COM_RESOURCES_MEDIA_DIRECTORY_NOT_EMPTY');?> '+numFiles);
			return false;
		}

		if (confirm('<?php echo Lang::txt('COM_RESOURCES_MEDIA_DELETE_DIRECTORY'); ?> "'+folder+'"')) {
			return true;
		}

		return false;
	}
</script>
<div id="attachments">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="filelist" name="filelist">
		<?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
			<p><?php echo Lang::txt('COM_RESOURCES_NO_FILES_FOUND'); ?></p>
		<?php } else { ?>
			<table>
				<tbody>
				<?php
				$folders = $this->folders;

				for ($i=0; $i<count($folders); $i++)
				{
					$folderName = key($folders);

					$numFiles = 0;
					if (is_dir($folderName))
					{
						$d = @dir($folderName);

						while (false !== ($entry = $d->read()))
						{
							if (substr($entry, 0, 1) != '.')
							{
								$numFiles++;
							}
						}
						$d->close();
					}

					if ($this->listdir == '/')
					{
						$this->listdir = '';
					}

					if (!isset($subdir) || $subdir == null)
					{
						$subdir = '';
					}
					?>
					<tr>
						<td>
							<span class="icon folder">
								<span><?php echo $folderName; ?></span>
							</span>
						</td>
						<td width="100%">
							<?php echo $folderName; ?>
						</td>
						<td>
							<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deletefolder&delFolder=' . DS . $folders[$folderName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $subdir . '&' . Session::getFormToken() . '=1'); ?>" target="filer" onclick="return deleteFolder('<?php echo $folderName; ?>', '<?php echo $numFiles; ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
							</a>
						</td>
					</tr>
					<?php
					next($folders);
				}
				$docs = $this->docs;
				for ($i=0; $i<count($docs); $i++)
				{
					$docName = key($docs);

					$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
					?>
					<tr>
						<td>
							<input type="radio" name="slctdfile" value="<?php echo $this->escape($this->listdir . $subdird . $docs[$docName]); ?>" />
						</td>
						<td width="100%">
							<?php echo $docs[$docName]; ?>
						</td>
						<td>
							<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deletefile&delFile=' . $docs[$docName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $this->subdir . '&' . Session::getFormToken() . '=1'); ?>" target="filer" onclick="return deleteFile('<?php echo $docs[$docName]; ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
							</a>
						</td>
					</tr>
					<?php
					next($docs);
				}
				?>
				</tbody>
			</table>
		<?php } ?>
	</form>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>