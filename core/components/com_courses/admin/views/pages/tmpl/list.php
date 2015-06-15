<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$app = \JFactory::getApplication();
?>
		<script type="text/javascript">
			/*function updateDir()
			{
				var allPaths = window.top.document.forms[0].dirPath.options;
				for (i=0; i<allPaths.length; i++)
				{
					allPaths.item(i).selected = false;
					if ((allPaths.item(i).value)== '<?php if (strlen($this->listdir)>0) { echo $this->listdir ;} else { echo '/';}  ?>') {
						allPaths.item(i).selected = true;
					}
				}
			}*/
			function deleteFile(file)
			{
				if (confirm("Delete file \""+file+"\"?")) {
					return true;
				}

				return false;
			}
			/*function deleteFolder(folder, numFiles)
			{
				if (numFiles > 0) {
					alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
					return false;
				}

				if (confirm('Delete folder "'+folder+'"?')) {
					return true;
				}

				return false;
			}*/
		</script>
	<div id="attachments">
		<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" id="filelist" name="filelist">
			<table summary="Files for this asset">
				<tbody>
<?php if (count($this->docs) == 0) { //count($this->folders) == 0 &&  ?>
					<tr>
						<td>
							<?php echo Lang::txt('No files found.'); ?>
						</td>
					</tr>
<?php } else { ?>
<?php
/*$folders = $this->folders;
for ($i=0; $i<count($folders); $i++)
{
	$folderName = key($folders);

	$numFiles = 0;
	if (is_dir(PATH_CORE . DS . $folders[$folderName]))
	{
		$d = @dir(PATH_CORE . DS . $folders[$folderName]);

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
	$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
?>
					<tr>
						<td style="width:16px;">
							<img src="<?php echo Request::base(true); ?>/core/components/<?php echo $this->option; ?>/admin/assets/img/folder.png" alt="<?php echo $folderName; ?>" width="16" height="16" />
						</td>
						<td>
							<?php echo $folderName; ?>
						</td>
						<td style="width:16px;">
							<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deletefolder&amp;delFolder=<?php echo DS . $folders[$folderName]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;tmpl=component&amp;subdir=<?php echo $this->subdir; ?>&amp;course=<?php echo $this->course_id; ?>&amp;<?php echo Session::getFormToken(); ?>=1" target="filelist" onclick="return deleteFolder('<?php echo $folderName; ?>', '<?php echo $numFiles; ?>');" title="<?php echo Lang::txt('DELETE'); ?>">
								<img src="<?php echo Request::base(true); ?>/core/components/<?php echo $this->option; ?>/admin/assets/img/trash.png" width="15" height="15" alt="<?php echo Lang::txt('DELETE'); ?>" />
							</a>
						</td>
					</tr>
<?php
	next($folders);
}*/
				$docs = $this->docs;
				for ($i=0; $i<count($docs); $i++)
				{
					$docName = key($docs);

					$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
				?>
					<tr>
						<?php /*<td style="width:16px;">
							<img src="<?php echo Request::base(true); ?>/core/components/<?php echo $this->option; ?>/admin/assets/img/file.png" alt="<?php echo $docName; ?>" width="16" height="16" />
						</td>*/ ?>
						<td>
							<?php echo $docs[$docName]; ?>
						</td>
						<td style="width:16px;">
							<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=deletefile&delFile=' . $docs[$docName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $this->subdir . '&course=' . $this->course_id . '&' . Session::getFormToken() . '=1'); ?>" target="filelist" onclick="return deleteFile('<?php echo $docs[$docName]; ?>');" title="<?php echo Lang::txt('DELETE'); ?>">
								<img src="<?php echo Request::base(true); ?>/core/components/<?php echo $this->option; ?>/admin/assets/img/trash.png" width="15" height="15" alt="<?php echo Lang::txt('DELETE'); ?>" />
							</a>
						</td>
					</tr>
				<?php
					next($docs);
				}
				?>
<?php } ?>
				</tbody>
			</table>
		</form>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	</div>