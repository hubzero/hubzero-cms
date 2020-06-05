<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('storage.css')
	->js('media.js');
?>
	<div id="small-page">
		<div class="databrowser">
			<div id="filelist">
				<table>
					<caption>
						<span class="icon-home home">
							<?php if (count($this->dirtree) > 0) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component'); ?>"><?php echo Lang::txt('COM_TOOLS_HOME'); ?></a>
							<?php } else { ?>
								<span><?php echo Lang::txt('COM_TOOLS_HOME'); ?></span>
							<?php } ?>
						</span>
						<?php
						if (count($this->dirtree) > 0)
						{
							$path = '';
							$i = 0;
							foreach ($this->dirtree as $branch)
							{
								if ($branch !='')
								{
									$path .= $branch . DS;
									$i++;
									?>
									<span class="arrow">&raquo;</span>
									<span class="icon-folder folder">
										<?php if ($i != count($this->dirtree)) { ?>
											<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component&listdir=' . $path); ?>"><?php echo ucfirst($branch); ?></a>
										<?php } else { ?>
											<span><?php echo ucfirst($branch); ?></span>
										<?php } ?>
									</span>
									<?php
								}
							}
						}
						?>
					</caption>
					<tbody>
					<?php
					foreach ($this->folders as $fullpath => $name)
					{
						$dir = DS . $name;
						$numFiles = count(\Filesystem::files($fullpath, '.', false, true, array()));

						if ($this->listdir == DS)
						{
							$this->listdir = '';
						}
						$d = ($this->listdir) ? $this->listdir . DS . $name : DS . $name;
					?>
						<tr>
							<td width="100%">
								<a class="icon-folder" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component&listdir=' . urlencode($d)); ?>">
									<?php echo $dir; ?>
								</a>
							</td>
							<td class="file-size">
							</td>
							<td>
								<?php if ($dir != '/data' && $dir != '/sessions') { ?>
									<a class="delete icon-delete delete-folder" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefolder&amp;delFolder=<?php echo urlencode($dir); ?>&amp;listdir=<?php echo urlencode($this->listdir); ?>&amp;tmpl=component" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the folder "%s"?', $dir); ?>" data-files="<?php echo $numFiles; ?>" data-notempty="<?php echo Lang::txt('Sorry unable to delete folder because it is not empty'); ?>" target="filer" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">

										<?php echo Lang::txt('JACTION_DELETE'); ?>
									</a>
								<?php } ?>
							</td>
						</tr>
					<?php
					}

					foreach ($this->docs as $fullpath => $name)
					{
					?>
						<tr>
							<td width="100%">
								<span class="icon-file"><?php echo $name; ?></span>
							</td>
							<td class="file-size">
								<?php echo \Hubzero\Utility\Number::formatBytes(filesize($fullpath)); ?>
							</td>
							<td>
								<a class="delete icon-delete delete-file" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefile&amp;file=<?php echo $name; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the file "%s"?', $name); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">

									<?php echo Lang::txt('JACTION_DELETE'); ?>
								</a>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		</div>
	</div>
