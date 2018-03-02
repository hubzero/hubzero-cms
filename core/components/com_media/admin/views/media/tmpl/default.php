<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEDIA'));
Toolbar::preferences($this->option, '550');
Toolbar::spacer();
Toolbar::deleteList('', 'delete');
Toolbar::spacer();
Toolbar::help('media');
?>
<script type="text/javascript">
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	submitform(pressbutton);
}
</script>
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
				<form action="<?php echo Route::url('index.php?option=com_media&task=ftpValidate'); ?>" name="ftpForm" id="ftpForm" method="post">
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

			<form action="<?php echo Route::url('index.php?option=com_media&' . Session::getFormToken() . '=1', true, true); ?>" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="token" value="<?php echo \App::get('session')->getFormToken(); ?>" />
				<input type="hidden" name="folder" id="folder" value="<?php echo $this->folder; ?>" />
			</form>
			<legend><?php echo Lang::txt('COM_MEDIA_FILES'); ?></legend>
			<fieldset id="folderview">
				<div class="view">
					<iframe src="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $this->folder); ?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
				</div>
			</fieldset>
<!--
<form action="<?php echo Route::url('index.php?option=com_media&controller=media&task=new');?>" name="folderForm" id="folderForm" method="post">
        <legend><?php echo Lang::txt('COM_MEDIA_CREATE_FOLDER'); ?></legend>
        <div class="path">
                <?php if (User::authorise('core.create', 'com_media')): ?>
                <input class="inputbox" type="text" id="folderpath" readonly="readonly" placeholder="<?php //echo Lang::txt('COM_MEDIA_FOLDER_PATH'); ?>" />
                <input class="inputbox" type="text" id="foldername" name="foldername" placeholder="<?php echo Lang::txt('COM_MEDIA_FOLDER_NAME'); ?>" />
                <input class="update-folder" type="hidden" name="parent" id="parent" value="<?php echo $this->parent; ?>" />
                <button type="submit"><?php echo Lang::txt('COM_MEDIA_CREATE'); ?></button>
                <?php endif; ?>
        </div>
        <?php echo Html::input('token'); ?>
</form>
<form action="<?php echo Route::url('index.php?option=com_media&controller=media&task=upload&' . Session::getFormToken() . '=1', true, true); ?>" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
        <legend><?php //echo $this->config->get('upload_maxsize')=='0' ? Lang::txt('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : Lang::txt('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
        <fieldset id="uploadform">
                <fieldset id="upload-noflash" class="actions">
                        <label for="upload-file" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_UPLOAD_FILE'); ?></label>
                        <input type="file" id="upload-file" name="Filedata[]" multiple="multiple" />
                        <label for="upload-submit" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?></label>
                        <input type="submit" id="upload-submit" value="<?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?>"/>
                </fieldset>
                <input class="hidden" type="hidden" name="folder" id="folder" value="<?php echo $this->folder; ?>" />

                <input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
        </fieldset>
</form>
-->
		</td>
	</tr>
	<?php echo Html::input('token'); ?>
</table>
