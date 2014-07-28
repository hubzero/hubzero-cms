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

JHTML::_('behavior.modal');

// push scripts and styles
$this->css()
     ->css('media.css')
     ->js()
     ->js('groups.medialist')
     ->js('jquery.fileuploader', 'system')
     ->js('jquery.contextMenu', 'system')
     ->css('jquery.contextMenu.css', 'system');

// define base URI
$baseURI  = ($_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
$baseURI .= $_SERVER['HTTP_HOST'] . DS . 'groups' . DS . $this->group->get('cn');

//get request vars
$type          = JRequest::getWord('type', '', 'get');
$ckeditor      = JRequest::getVar('CKEditor', '', 'get');
$ckeditorFunc  = JRequest::getInt('CKEditorFuncNum', 0, 'get');
$ckeditorQuery = '&type='.$type.'&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;
?>

<script type="text/javascript">
	function ckeditorInsertFile( file )
	{
		var opener = window.parent;
		HUB.GroupsMediaList.ckeditorInsert( file, opener );
	}
</script>


<div class="upload-filelist-toolbar">
	<div class="toolbar cf">
		<?php
			$folder   = '';
			$segments = explode('/', ltrim($this->relpath, DS));
		?>
		<ul class="path">
			<?php if ($this->group->get('type') == 3) : ?>
				<li><a data-folder="/" href="javascript:(void);"><?php echo JText::_('COM_GROUPS_MEDIA_PATH_SLASH_ROOT'); ?></a></li>
			<?php endif; ?>
			<?php foreach ($segments as $segment) : ?>
				<?php $folder .= DS . $segment; ?>
				<li class="divider"><?php echo JText::_('COM_GROUPS_MEDIA_PATH_SLASH'); ?></li>
				<li><a data-folder="<?php echo $folder; ?>" href="javascript:(void);"><?php echo $segment; ?></a></li>
			<?php endforeach; ?>
		</ul>
		<div class="buttons"></div>
	</div>
	<div class="filelist-headers">
		<ul>
			<li>
				<div class="name"><?php echo JText::_('COM_GROUPS_MEDIA_NAME'); ?></div>
				<div class="modified"><?php echo JText::_('COM_GROUPS_MEDIA_MODIFIED'); ?></div>
			</li>
		</ul>
	</div>
</div>

<div class="upload-filelist">
	<ul>
		<?php foreach ($this->folders as $folder) : ?>
			<li class="folder">
				<div class="name">
					<?php
						$dataFolder = $this->relpath . $folder;
						if ($this->relpath != '/')
						{
							$dataFolder = $this->relpath . DS . $folder;
						}

						$moveFolderPath     = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=movefolder&folder=' .  $dataFolder . '&tmpl=component');
						$renameFolderPath   = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=renamefolder&folder=' .  $dataFolder . '&tmpl=component');
						$deleteFolderPath   = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=deletefolder&folder=' . $dataFolder . '&tmpl=component');
					?>
					<a href="javascript:void(0);" data-action-delete="<?php echo $deleteFolderPath; ?>" data-action-rename="<?php echo $renameFolderPath; ?>" data-action-move="<?php echo $moveFolderPath; ?>" data-folder="<?php echo $dataFolder; ?>"><?php echo $folder; ?></a>
				</div>
				<div class="modified">
					--
				</div>
			</li>
		<?php endforeach; ?>

		<?php foreach ($this->files as $file) : ?>
			<?php
				// build file path
				$filePath    = $this->path . DS . $file;
				$relFilePath = $this->relpath . DS . $file;

				// get file info & stats
				$fileInfo   = @pathInfo($filePath);
				$filesize   = @filesize($filePath);
				$dimensions = @getimagesize( $filePath );
				$modified   = @filemtime($filePath);


				// formatted results
				$extension           = $fileInfo['extension'];
				$formattedFilesize   = \Hubzero\Utility\Number::formatBytes( $filesize );
				$formattedDimensions = $dimensions[0] . 'px &times; ' . $dimensions[1] . 'px';
				$formattedModified   = date('m/d/Y g:ia', $modified);

				// is this file an image
				$isImage   = (in_array($extension, array('jpg','jpeg','png','gif','bmp','tiff'))) ? true : false;
				$isArchive = (in_array($extension, array('zip', 'tar', 'gz'))) ? true : false;

				// build paths
				$downloadPath = $baseURI . DS . 'File:' . $relFilePath;
				$movePath     = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=movefile&file=' .  $relFilePath . '&tmpl=component');
				$renamePath   = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=renamefile&file=' .  $relFilePath . '&tmpl=component');
				$extractPath  = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=extractfile&file=' . $relFilePath . '&tmpl=component');
				$deletePath   = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=deletefile&file=' . $relFilePath . '&tmpl=component');
				//$rawPath  = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=rawfile&file=' . $relFilePath . '&tmpl=component');
			?>
			<li class="file file-<?php echo strtolower($extension); ?>">
				<div class="name">
					<a href="javascript:void(0);"><?php echo $file; ?></a>
				</div>
				<div class="modified">
					<?php echo ($modified) ? $formattedModified : '--'; ?>
				</div>
			</li>
			<li class="file-details cf">
				<div class="right">
					<div class="title"><?php echo JText::_('COM_GROUPS_MEDIA_FILE_PREVIEW'); ?></div>
					<div class="preview">
						<?php if ($isImage) : ?>
							<img src="/components/com_groups/assets/img/loading.gif" data-src="<?php echo $downloadPath; ?>" />
						<?php else : ?>
							<p><strong><?php echo JText::_('COM_GROUPS_MEDIA_FILE_PREVIEW_NOT_AVAILABLE'); ?></strong></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="left">
					<div class="title"><?php echo JText::_('COM_GROUPS_MEDIA_FILE_DETAILS'); ?></div>
					<ul>
						<li>
							<strong><?php echo JText::_('COM_GROUPS_MEDIA_FILE_NAME'); ?>: </strong> <?php echo $file; ?>
						</li>
						<li>
							<strong><?php echo JText::_('COM_GROUPS_MEDIA_FILE_SIZE'); ?>: </strong> <?php echo $formattedFilesize; ?>
						</li>
						<?php if ($isImage) : ?>
							<li>
								<strong><?php echo JText::_('COM_GROUPS_MEDIA_FILE_DIMENSIONS'); ?>: </strong> <?php echo $formattedDimensions; ?>
							</li>
						<?php endif; ?>
						<li class="path">
							<strong><?php echo JText::_('COM_GROUPS_MEDIA_FILE_PATH'); ?>: </strong> <span><?php echo $downloadPath; ?></span>
						</li>
						<li>
							<?php if (isset($ckeditor) && $ckeditor != '') : ?>
								<a href="javascript:void(0);" class="btn icon-add" onclick="return ckeditorInsertFile('<?php echo $downloadPath; ?>');"><?php echo JText::_('COM_GROUPS_MEDIA_INSERT_FILE'); ?></a>
							<?php endif; ?>
							<a href="<?php echo $downloadPath; ?>" class="btn icon-download action-download"><?php echo JText::_('COM_GROUPS_MEDIA_DOWNLOAD'); ?></a>
							<a href="<?php echo $renamePath; ?>" class="btn icon-edit action-rename"><?php echo JText::_('COM_GROUPS_MEDIA_RENAME'); ?></a>
							<a href="<?php echo $movePath; ?>" class="btn icon-move action-move"><?php echo JText::_('COM_GROUPS_MEDIA_MOVE'); ?></a>
							<?php if ($isArchive) : ?>
								<a href="<?php echo $extractPath; ?>" class="btn icon-extract action-extract"><?php echo JText::_('COM_GROUPS_MEDIA_EXTRACT'); ?></a>
							<?php endif; ?>
							<a data-file="<?php echo $relFilePath; ?>" href="<?php echo $deletePath; ?>" class="btn icon-delete action-delete">
								<?php echo JText::_('COM_GROUPS_MEDIA_DELETE'); ?></a>
						</li>
					</ul>
				</div>
			</li>
		<?php endforeach; ?>
		<?php if (count($this->folders) == 0 && count($this->files) == 0) : ?>
			<li><em><?php echo JText::_('COM_GROUPS_MEDIA_NO_FILES'); ?></em></li>
		<?php endif;?>
	</ul>
</div>