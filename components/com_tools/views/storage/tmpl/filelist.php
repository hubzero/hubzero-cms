<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css('storage.css');
?>
	<div id="small-page">
		<div class="databrowser">
			<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" id="filelist">
				<table>
					<caption>
						<span class="icon-home home">
							<?php if (count($this->dirtree) > 0) { ?>
								<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component'); ?>"><?php echo JText::_('COM_TOOLS_HOME'); ?></a>
							<?php } else { ?>
								<span><?php echo JText::_('COM_TOOLS_HOME'); ?></span>
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
											<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component&listdir=' . $path); ?>"><?php echo ucfirst($branch); ?></a>
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
						$numFiles = count(JFolder::files($fullpath, '.', false, true, array()));

						if ($this->listdir == DS)
						{
							$this->listdir = '';
						}
						$d = ($this->listdir) ? $this->listdir . DS . $name : DS . $name;
					?>
						<tr>
							<td width="100%">
								<a class="icon-folder" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component&listdir=' . urlencode($d)); ?>">
									<?php echo $dir; ?>
								</a>
							</td>
							<td class="file-size">
							</td>
							<td>
								<?php if ($dir != '/data' && $dir != '/sessions') { ?>
									<a class="delete icon-delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefolder&amp;delFolder=<?php echo urlencode($dir); ?>&amp;listdir=<?php echo urlencode($this->listdir); ?>&amp;tmpl=component" target="filer" onclick="return deleteFolder('<?php echo $dir; ?>', <?php echo $numFiles; ?>);" title="<?php echo JText::_('JACTION_DELETE'); ?>">
										<?php echo JText::_('JACTION_DELETE'); ?>
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
								<a class="delete icon-delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefile&amp;file=<?php echo $name; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $name; ?>');" title="<?php echo JText::_('JACTION_DELETE'); ?>">
									<?php echo JText::_('JACTION_DELETE'); ?>
								</a>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</form>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		</div>
	</div>