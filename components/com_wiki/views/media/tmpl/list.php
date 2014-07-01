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

$no_html = JRequest::getVar('no_html', 0);

if (!$no_html) {
	$this->css();
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
			alert('<?php echo JText::_('COM_WIKI_WARNING_FOLDER_NOT_EMPTY'); ?>');
			return false;
		}

		if (confirm('<?php echo JText::_('COM_WIKI_WARNING_DELETE_FOLDER'); ?> "'+folder+'"')) {
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
			<p><?php echo JText::_('COM_WIKI_ERROR_NO_FILES_FOUND'); ?></p>
<?php } else { ?>
			<table>
				<tbody>
				<?php
				if ($this->docs)
				{
					jimport('joomla.filesystem.file');
					$base = JURI::base(true);

					foreach ($this->docs as $path => $name)
					{
						$ext = JFile::getExt($name);
				?>
					<tr>
						<td>
							<span><?php echo $this->escape(stripslashes($name)); ?></span>
						</td>
						<td>
							<a class="icon-delete delete" href="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=deletefile&amp;file=<?php echo $name; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;<?php echo (!$no_html) ? 'tmpl=component' : 'no_html=1'; ?>" <?php if (!$no_html) { ?>target="filer" onclick="return deleteFile('<?php echo $this->escape($name); ?>');"<?php } ?> title="<?php echo JText::_('JACTION_DELETE'); ?>">
								<?php echo JText::_('JACTION_DELETE'); ?>
							</a>
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