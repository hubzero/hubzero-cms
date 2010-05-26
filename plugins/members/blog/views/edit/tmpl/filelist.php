<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo JText::_('MEMBERS_FILE_MANAGER'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
<?php if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'members.css')) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS; ?>members.css" />
<?php } else { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DS.'components'.DS.$this->option.DS; ?>members.css" />
<?php } ?>
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
		</script>
	</head>
	<body id="attachments">
		<form action="index.php" method="post" id="filelist">
<?php if (count($this->images) == 0 && count($this->folders) == 0 && count($this->docs) == 0) { ?>
			<p><?php echo JText::_('NO_FILES_FOUND'); ?></p>
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
	
	if ($listdir == '/') {
		$listdir = '';
	}
?>
					<tr>
						<td><img src="/components/com_blog/images/icons/folder.gif" alt="<?php echo $folder_name; ?>" width="16" height="16" /></td>
						<td width="100%"><?php echo $folder_name; ?></td>
						<td><a href="/index.php?option=<?php echo $this->option; ?>'&amp;task=deletefolder&amp;folder=<?php echo DS.$folders[$folder_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1" target="filer" onclick="return deleteFolder('<?php echo $folder_name; ?>', '<?php echo $num_files; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/images/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
					</tr>
<?php
	next($folders);
}
$docs = $this->docs;
for ($i=0; $i<count($docs); $i++) 
{
	$doc_name = key($docs);	
	$iconfile = DS.'components'.DS.$this->option.DS.'images'.DS.'icons'.DS.substr($doc_name,-3).'.png';
	//$iconfile = $this->config->get('iconpath').DS.substr($doc_name,-3).'.png';

	if (file_exists(JPATH_ROOT.$iconfile))	{
		$icon = $iconfile;
	} else {
		$icon = DS.'components'.DS.$this->option.DS.'images'.DS.'icons'.DS.'unknown.png';
		//$icon = $this->config->get('iconpath').DS.'unknown.png';
	}
?>
					<tr>
						<td><img src="<?php echo $icon; ?>" alt="<?php echo $docs[$doc_name]; ?>" width="16" height="16" /></td>
						<td width="100%"><?php echo $docs[$doc_name]; ?></td>
						<td><a href="/index.php?option=<?php echo $this->option; ?>&amp;task=deletefile&amp;file=<?php echo $docs[$doc_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1" target="filer" onclick="return deleteFile('<?php echo $docs[$doc_name]; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/images/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
					</tr>
<?php
	next($docs);
}
$images = $this->images;
for ($i=0; $i<count($images); $i++) 
{
	$image_name = key($images);
	$iconfile = DS.'components'.DS.$this->option.DS.'images'.DS.'icons'.DS.substr($image_name,-3).'.png';
	if (file_exists(JPATH_ROOT.$iconfile))	{
		$icon = $iconfile;
	} else {
		$icon = DS.'components'.DS.$this->option.DS.'images'.DS.'icons'.DS.'unknown.png';
		//$icon = $this->config->get('iconpath').DS.'unknown.png';
	}
?>
					<tr>
						<td><img src="<?php echo $icon; ?>" alt="<?php echo $images[$image_name]; ?>" width="16" height="16" /></td>
						<td width="100%"><?php echo $images[$image_name]; ?></td>
						<td><a href="/index.php?option=<?php echo $this->option; ?>&amp;task=deletefile&amp;file=<?php echo $images[$image_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1" target="filer" onclick="return deleteFile('<?php echo $images[$image_name]; ?>');" title="<?php echo JText::_('DELETE'); ?>"><img src="/components/<?php echo $this->option; ?>/images/icons/trash.gif" width="15" height="15" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
					</tr>
<?php
	next($images);
}
?>
				</tbody>
			</table>
<?php } ?>
		</form>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	</body>
</html>