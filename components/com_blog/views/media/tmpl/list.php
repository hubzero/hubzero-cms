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
?>
		<script type="text/javascript">
			function updateDir()
			{
				var allPaths = window.top.document.forms[0].dirPath.options;
				for (i=0; i<allPaths.length; i++)
				{
					allPaths.item(i).selected = false;
					if ((allPaths.item(i).value)== '<?php if (strlen($this->id)>0) { echo $this->id ;} else { echo '/';}  ?>') {
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
					alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
					return false;
				}

				if (confirm('Delete folder "'+folder+'"?')) {
					return true;
				}

				return false;
			}
		</script>
	<div id="attachments">
		<form action="index.php" method="post" id="filelist">
<?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
			<p><?php echo JText::_('COM_BLOG_NO_FILES_FOUND'); ?></p>
<?php } else { ?>
			<table summary="<?php echo JText::_('Files for this blog'); ?>">
				<tbody>
<?php
foreach ($this->folders as $k => $folder)
{
	$num_files = 0;

	if (is_dir(JPATH_ROOT . DS . $folder)) 
	{
		$d = @dir(JPATH_ROOT . DS . $folder);

		while (false !== ($entry = $d->read()))
		{
			if (substr($entry,0,1) != '.') 
			{
				$num_files++;
			}
		}
		$d->close();
	}
?>
					<tr>
						<td>
							<img src="/components/<?php echo $this->option; ?>/assets/img/icons/folder.gif" alt="<?php echo $k; ?>" width="16" height="16" />
						</td>
						<td width="100%">
							<span class="folder">
								<?php echo $k; ?>
							</span>
						</td>
						<td>
							<a href="/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefolder&amp;folder=<?php echo DS . $folder; ?>&amp;scope=<?php echo $this->scope; ?>&amp;id=<?php echo $this->id; ?>&amp;tmpl=component" target="filer" onclick="return deleteFolder('<?php echo $folder; ?>', '<?php echo $num_files; ?>');" title="<?php echo JText::_('DELETE'); ?>">
								<img src="/components/<?php echo $this->option; ?>/assets/img/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" />
							</a>
						</td>
					</tr>
<?php
}

jimport('joomla.filesystem.file');
foreach ($this->docs as $name => $doc)
{
	$ext = JFile::getExt($doc);

	$iconfile = DS . 'components' . DS . $this->option . DS . 'assets' . DS . 'img' . DS . 'icons' . DS . substr($doc, 0, (strlen($doc) - (strlen($ext) + 1))) . '.png';

	if (file_exists(JPATH_ROOT . $iconfile)) 
	{
		$icon = $iconfile;
	} 
	else 
	{
		$icon = DS . 'components' . DS . $this->option . DS . 'assets' . DS . 'img' . DS . 'icons' . DS . 'unknown.png';
	}
?>
					<tr>
						<td>
							<img src="<?php echo $icon; ?>" alt="<?php echo $doc; ?>" width="16" height="16" />
						</td>
						<td width="100%">
							<span class="file <?php echo $ext; ?>">
								<?php echo $this->escape($doc); ?>
							</span>
						</td>
						<td>
							<a class="delete" href="/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefile&amp;file=<?php echo $doc; ?>&amp;scope=<?php echo $this->scope; ?>&amp;id=<?php echo $this->id; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $doc; ?>');" title="<?php echo JText::_('DELETE'); ?>">
								<img src="/components/<?php echo $this->option; ?>/assets/img/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" />
							</a>
						</td>
					</tr>
<?php
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