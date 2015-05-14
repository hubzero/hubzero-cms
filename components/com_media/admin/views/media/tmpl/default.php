<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

?>
<table width="100%">
	<tr valign="top">
		<td class="media-tree">
			<fieldset id="treeview">
				<legend><?php echo Lang::txt('COM_MEDIA_FOLDERS'); ?></legend>
				<div id="media-tree_tree"></div>
				<?php echo $this->loadTemplate('folders'); ?>
			</fieldset>
		</td>
		<td class="media-browser">
			<?php if ((User::authorise('core.create', 'com_media')) and $this->require_ftp): ?>
				<form action="index.php?option=com_media&amp;task=ftpValidate" name="ftpForm" id="ftpForm" method="post">
					<fieldset title="<?php echo Lang::txt('COM_MEDIA_DESCFTPTITLE'); ?>">
						<legend><?php echo Lang::txt('COM_MEDIA_DESCFTPTITLE'); ?></legend>
						<?php echo Lang::txt('COM_MEDIA_DESCFTP'); ?>
						<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></label>
						<input type="text" id="username" name="username" class="inputbox" size="70" value="" />

						<label for="password"><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></label>
						<input type="password" id="password" name="password" class="inputbox" size="70" value="" />
					</fieldset>
				</form>
			<?php endif; ?>

			<form action="<?php echo Route::url('index.php?option=com_media'); ?>" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data" >
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="cb1" id="cb1" value="0" />
				<input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->folder; ?>" />
			</form>

			<form action="<?php echo Route::url('index.php?option=com_media&task=folder.create&tmpl=' . Request::getCmd('tmpl', 'index'));?>" name="folderForm" id="folderForm" method="post">
				<fieldset id="folderview">
					<div class="view">
						<iframe src="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->state->folder); ?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
					</div>
					<legend><?php echo Lang::txt('COM_MEDIA_FILES'); ?></legend>
					<div class="path">
					<?php if (User::authorise('core.create', 'com_media')): ?>
						<input class="inputbox" type="text" id="folderpath" readonly="readonly" placeholder="<?php echo Lang::txt('COM_MEDIA_FOLDER_PATH'); ?>" />
						<input class="inputbox" type="text" id="foldername" name="foldername" placeholder="<?php echo Lang::txt('COM_MEDIA_FOLDER_NAME'); ?>" />
						<input class="update-folder" type="hidden" name="folderbase" id="folderbase" value="<?php echo $this->state->folder; ?>" />
						<button type="submit"><?php echo Lang::txt('COM_MEDIA_CREATE_FOLDER'); ?></button>
					<?php endif; ?>
					</div>
					<?php echo JHtml::_('form.token'); ?>
				</fieldset>
			</form>

			<?php if (User::authorise('core.create', 'com_media')):?>
			<!-- File Upload Form -->
			<form action="<?php echo Route::url('index.php?option=com_media&task=file.upload&tmpl=component&' . $this->session->getName().'='.$this->session->getId() . '&' . Session::getFormToken() . '=1&format=html'); ?>" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
				<fieldset id="uploadform">
					<legend><?php echo $this->config->get('upload_maxsize')=='0' ? Lang::txt('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : Lang::txt('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
					<fieldset id="upload-noflash" class="actions">
						<label for="upload-file" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_UPLOAD_FILE'); ?></label>
						<div class="input-modal">
							<span class="input-cell">
								<input type="file" id="upload-file" name="Filedata[]" multiple="multiple" />
							</span>
							<span class="input-cell">
								<label for="upload-submit" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?></label>
								<input type="submit" id="upload-submit" value="<?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?>"/>
							</span>
						</div>
					</fieldset>
					<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
				</fieldset>
			</form>
			<?php endif;?>
		</td>
	</tr>
</table>
