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

$this->css('storage.css');
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
									<a class="delete icon-delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefolder&amp;delFolder=<?php echo urlencode($dir); ?>&amp;listdir=<?php echo urlencode($this->listdir); ?>&amp;tmpl=component" target="filer" onclick="return deleteFolder('<?php echo $dir; ?>', <?php echo $numFiles; ?>);" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
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
								<a class="delete icon-delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefile&amp;file=<?php echo $name; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $name; ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
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