<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->js('media.js');
?>
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

					$p = strpos($folderName, $this->listdir);
					$p = intval($p) + strlen($this->listdir);
					$name = substr($folderName, $p);
					?>
					<tr>
						<td>
							<span class="icon folder">
								<span><?php echo $name; ?></span>
							</span>
						</td>
						<td width="100%">
							<?php //echo $folderName; ?>
						</td>
						<td>
							<a class="delete-folder state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deletefolder&delFolder=' . DS . $folders[$folderName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $subdir . '&' . Session::getFormToken() . '=1'); ?>" target="filer" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the folder "%s"?', $folderName); ?>" data-files="<?php echo $numFiles; ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
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
							<a class="delete-file state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deletefile&delFile=' . $docs[$docName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $this->subdir . '&' . Session::getFormToken() . '=1'); ?>" target="filer" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the file "%s"?', $docs[$docName]); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
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
