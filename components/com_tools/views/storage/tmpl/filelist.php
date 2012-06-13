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

ximport('Hubzero_View_Helper_Html');
$app =& JFactory::getApplication();
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
				/*if (numFiles > 0) {
					alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
					return false;
				}*/

				if (confirm('Delete folder "'+folder+'" and all files it may contain?')) {
					return true;
				}

				return false;
			}
		</script>
	<div id="small-page">
		<div class="databrowser">
			<form action="index.php" method="post" id="filelist">

				<ul id="filepath">
					<li class="home">
<?php if (count($this->dirtree) > 0) { ?>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=listfiles&tmpl=component'); ?>"><?php echo JText::_('Home'); ?></a>
<?php } else { ?>
						<span><?php echo JText::_('Home'); ?></span>
<?php } ?>
					</li>
<?php
						if (count($this->dirtree) > 0) {
							$path = '';
							$i = 0;
							foreach ($this->dirtree as $branch)
							{
								if ($branch !='') {
									$path .= $branch.DS;
									$i++;
?>
					<li class="arrow">&raquo;</li>
					<li class="folder">
<?php 							if ($i != count($this->dirtree)) { ?>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=listfiles&tmpl=component&listdir='.$path); ?>"><?php echo ucfirst($branch); ?></a>
<?php 							} else { ?>
						<span><?php echo ucfirst($branch); ?></span>
<?php 							} ?>
					</li>
<?php
								}
							}
						}
?>
				</ul>
				<table summary="User files">
					<tbody>
<?php
$folders = $this->folders;
for ($i=0; $i<count($folders); $i++)
{
	$folder_name = key($folders);
	$dir = DS.$folders[$folder_name];
	$num_files = count(JFolder::files($this->path . $dir, '.', false, true, array()));

	$d = ($this->listdir) ? $this->listdir.$dir : $dir;
	if ($this->listdir == '/') {
		$this->listdir = '';
	}
?>
						<tr>
							<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=listfiles&no_html=1&amp;listdir='.$d); ?>"><img src="/components/<?php echo $this->option; ?>/images/folder.gif" alt="<?php echo $folder_name; ?>" width="16" height="16" /></td>
							<td width="100%"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=listfiles&tmpl=component&listdir='.urlencode($d)); ?>"><?php echo $dir; ?></a></td>
							<td class="file-size"><?php echo Hubzero_View_Helper_Html::formatSize(Hubzero_View_Helper_Html::filesize_r( $this->path.DS.$folders[$folder_name] )); ?></td>
<?php if ($dir != '/data' && $dir != '/sessions') { ?>
							<td><a href="index.php?option=<?php echo $this->option; ?>&amp;task=deletefolder&amp;delFolder=<?php echo urlencode($dir); ?>&amp;listdir=<?php echo urlencode($this->listdir); ?>&amp;tmpl=component" target="filer" onclick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);" title="<?php echo JText::_('Delete'); ?>"><img src="components/<?php echo $this->option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('Delete'); ?>" /></a></td>
<?php } else { ?>
							<td> </td>
<?php } ?>
						</tr>
<?php
	next($folders);
}
$docs = $this->docs;
for ($i=0; $i<count($docs); $i++)
{
	$doc_name = key($docs);
	/*$iconfile = $this->config->get('iconpath').DS.substr($doc_name,-3).'.png';

	if (file_exists(JPATH_ROOT.$iconfile))	{
		$icon = $iconfile;
	} else {
		$icon = $this->config->get('iconpath').DS.'unknown.png';
	}*/
	$icon = DS.'templates'.DS. $app->getTemplate() .DS.'images'.DS.'icons'.DS.'16x16'.DS.substr($doc_name,-3).'.png';
	if (!file_exists($icon))	{
		$icon = DS.'templates'.DS. $app->getTemplate() .DS.'images'.DS.'icons'.DS.'16x16'.DS.'unknown.png';
	}
?>
						<tr>
							<td><img src="<?php echo $icon; ?>" alt="<?php echo $docs[$doc_name]; ?>" width="16" height="16" /></td>
							<td width="100%"><?php echo $docs[$doc_name]; ?></td>
							<td class="file-size"><?php echo Hubzero_View_Helper_Html::formatSize(filesize( $this->path.DS.$docs[$doc_name] )); ?></td>
							<td><a href="/index.php?option=<?php echo $this->option; ?>&amp;task=deletefile&amp;file=<?php echo $docs[$doc_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $docs[$doc_name]; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
						</tr>
<?php
	next($docs);
}
$images = $this->images;
for ($i=0; $i<count($images); $i++)
{
	$image_name = key($images);
	/*$iconfile = $this->config->get('iconpath').DS.substr($image_name,-3).'.png';
	if (file_exists(JPATH_ROOT.$iconfile))	{
		$icon = $iconfile;
	} else {
		$icon = $this->config->get('iconpath').DS.'unknown.png';
	}*/
	$icon = DS.'templates'.DS. $app->getTemplate() .DS.'images'.DS.'icons'.DS.'16x16'.DS.substr($doc_name,-3).'.png';
	if (!file_exists($icon))	{
		$icon = DS.'templates'.DS. $app->getTemplate() .DS.'images'.DS.'icons'.DS.'16x16'.DS.'unknown.png';
	}
?>
						<tr>
							<td><img src="<?php echo $icon; ?>" alt="<?php echo $images[$image_name]; ?>" width="16" height="16" /></td>
							<td width="100%"><?php echo $images[$image_name]; ?></td>
							<td class="file-size"><?php echo Hubzero_View_Helper_Html::formatSize(filesize( $this->path.DS.$images[$image_name] )); ?></td>
							<td><a href="/index.php?option=<?php echo $this->option; ?>&amp;task=deletefile&amp;file=<?php echo $images[$image_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component" target="filer" onclick="return deleteFile('<?php echo $images[$image_name]; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/images/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
						</tr>
<?php
	next($images);
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
