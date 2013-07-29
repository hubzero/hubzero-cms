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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$app =& JFactory::getApplication();

ximport('Hubzero_Group');
$group = Hubzero_Group::getInstance($this->listdir);
?>
		
	<div id="file_list">
		<form action="index.php" method="post" id="filelist">
<?php if (count($this->images) == 0 && count($this->folders) == 0 && count($this->docs) == 0) { ?>
			<p><?php echo JText::_('COM_GROUPS_MEDIA_NO_FILES'); ?></p>
<?php } else { ?>
			<table summary="Files for this group">
				<tbody>
<?php
$folders = $this->folders;
for ($i=0; $i<count($folders); $i++)
{
	$folder_name = key($folders);

	$num_files = 0;
	if (is_dir(JPATH_ROOT.DS.$folders[$folder_name])) {
		$d = @dir(JPATH_ROOT.DS.$folders[$folder_name]);

		while (false !== ($entry = $d->read()))
		{
			if (substr($entry,0,1) != '.') {
				$num_files++;
			}
		}
		$d->close();
	}

	if ($this->listdir == '/') {
		$this->listdir = '';
	}
?>
					<tr>
						<td><img src="<?php echo $this->config->get('iconpath'); ?>/folder.gif" alt="<?php echo $folder_name; ?>" width="16" height="16" /></td>
						<td width="100%"><?php echo $folder_name; ?></td>
						<td><a href="/index.php?option=<?php echo $this->option; ?>&amp;task=deletefolder&amp;folder=<?php echo DS.$folders[$folder_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFolder('<?php echo $folder_name; ?>', '<?php echo $num_files; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/assets/img/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
					</tr>
<?php
	next($folders);
}
$docs = $this->docs;
for ($i=0; $i<count($docs); $i++)
{
	$doc_name = key($docs);
	$iconfile = $this->config->get('iconpath').DS.substr($doc_name,-3).'.png';

	if (file_exists(JPATH_ROOT.$iconfile))	{
		$icon = $iconfile;
	} else {
		$icon = $this->config->get('iconpath').DS.'unknown.png';
	}
?>
					<tr>
						<td class="file-icon">
							<img src="<?php echo $icon; ?>" alt="<?php echo $docs[$doc_name]; ?>" width="16" height="16" />
						</td>
						<td class="file-delete">
							<a href="/index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=deletefile&amp;file=<?php echo $docs[$doc_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $docs[$doc_name]; ?>');" title="<?php echo JText::_('DELETE'); ?>">
								<img src="/components/<?php echo $this->option; ?>/assets/img/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" />
							</a>
						</td>
						<td class="file-name">
							<?php echo $docs[$doc_name]; ?>
						</td>
						<td class="file-path">
							<?php if(is_object($group)) : ?>
								<a href="#" onclick="return showFilePath('<?php echo 'https://'.$_SERVER['HTTP_HOST'].DS.'groups'.DS.$group->get('cn').DS.'File:'.$docs[$doc_name]; ?>')" title="Show File Path">
									<img src="/components/com_groups/assets/img/icons/file_path.png" alt="Show Image Path" width="15" height="15" />
								</a>
							<?php endif; ?>
						</td>
					</tr>
<?php
	next($docs);
}
$images = $this->images;
for ($i=0; $i<count($images); $i++)
{
	$image_name = key($images);
	$iconfile = $this->config->get('iconpath').DS.substr($image_name,-3).'.png';
	if (file_exists(JPATH_ROOT.$iconfile))	{
		$icon = $iconfile;
	} else {
		$icon = $this->config->get('iconpath').DS.'unknown.png';
	}
?>
					<tr>
						<td><img src="<?php echo $icon; ?>" alt="<?php echo $images[$image_name]; ?>" width="16" height="16" /></td>
						<td width="100%"><?php echo $images[$image_name]; ?></td>
						<td>
							<?php if(is_object($group)) : ?>
							<a href="#" onclick="return showFilePath('<?php echo 'https://'.$_SERVER['HTTP_HOST'].DS.'groups'.DS.$group->get('cn').DS.'Image:'.$images[$image_name]; ?>')" title="Show File Path"><img src="/components/com_groups/assets/img/icons/file_path.png" alt="Show Image Path" width="15" height="15" /></a>
							<?php endif; ?>
						</td>
						<td><a href="/index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=deletefile&amp;file=<?php echo $images[$image_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $images[$image_name]; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/assets/img/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
					</tr>
<?php
	next($images);
}
?>
				</tbody>
			</table>
<?php } ?>
		</form>
	</div>
	
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
			alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
			return false;
		}

		if (confirm('Delete folder "'+folder+'"?')) {
			return true;
		}

		return false;
	}
	
	function showFilePath(file) {
		var path = prompt('The file path is:', file);
		return false;
	}
	
</script>