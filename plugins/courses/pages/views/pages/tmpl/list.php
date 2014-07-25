<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$no_html = JRequest::getVar('no_html', 0);

$section = JRequest::getInt('section_id', 0);

$base = $this->offering->link() . '&active=pages';

if (!$no_html) { ?>
<script type="text/javascript">
	function updateDir()
	{
		var allPaths = window.top.document.forms[0].dirPath.options;
		for (i=0; i<allPaths.length; i++)
		{
			allPaths.item(i).selected = false;
			if ((allPaths.item(i).value)== '<?php if (isset($this->listdir)) { echo $this->listdir ;} else { echo '/';}  ?>') {
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
<?php } ?>
	<div id="attachments">
<?php if (!$no_html) { ?>
		<form action="index.php" method="post" id="filelist">
<?php } ?>
<?php if (count($this->docs) == 0) { ?>
			<p><?php echo JText::_('PLG_COURSES_PAGES_NO_FILES_FOUND'); ?></p>
<?php } else { ?>
			<table>
				<tbody>
				<?php
				if ($this->docs)
				{
					jimport('joomla.filesystem.file');

					$page = JRequest::getVar('page', '');

					foreach ($this->docs as $path => $name)
					{
						$ext = JFile::getExt($name);
						$type = (in_array($ext, array('jpg', 'jpe', 'jpeg', 'gif', 'png')) ? 'Image' : 'File');
				?>
					<tr class="row-group start">
						<td width="100%">
							<span><?php echo $this->escape(stripslashes($name)); ?></span>
						</td>
						<td>
							<a class="delete" href="<?php echo JRoute::_($base . '&action=remove&file=' . urlencode(stripslashes($name)) . '&' . (!$no_html ? 'tmpl=component' : 'no_html=1') . '&section_id=' . $section); ?>" <?php if (!$no_html) { ?>target="filer" onclick="return deleteFile('<?php echo $this->escape($name); ?>');"<?php } ?> title="<?php echo JText::_('PLG_COURSES_PAGES_DELETE'); ?>">
								<?php echo JText::_('PLG_COURSES_PAGES_DELETE'); ?>
							</a>
						</td>
					</tr>
					<tr class="row-group end">
						<td colspan="2">
							<span class="file-path"><?php echo JRoute::_($base . '&unit=download&b=' . $type . ':' . $this->escape(stripslashes($name))); ?></span>
						</td>
					</tr>
				<?php
					}
				}
				?>
				</tbody>
			</table>
<?php } ?>
<?php if (!$no_html) { ?>
		</form>
<?php } ?>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	</div>