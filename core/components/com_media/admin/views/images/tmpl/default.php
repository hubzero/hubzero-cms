<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

?>
<script type='text/javascript'>
var image_base_path = '<?php $params = Component::params('com_media');
echo substr(PATH_APP, strlen(PATH_ROOT)) . "/" . ltrim($params->get('image_path', 'images'), "/");?>/';
</script>
<h2 class="modal-title"><?php echo Lang::txt('COM_MEDIA'); ?></h2>

<form action="<?php echo Route::url('index.php?option=com_media&asset=' . Request::getCmd('asset') . '&author=' . Request::getCmd('author'));?>" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo Html::asset('image', 'media/dots.gif', '...', array('width' =>22, 'height' => 12), true)?>
	</div>
	<fieldset class="image-controls">
		<div class="fltlft">
			<label for="folder"><?php echo Lang::txt('COM_MEDIA_DIRECTORY') ?></label>
			<?php echo $this->folderList; ?>
			<button type="button" id="upbutton" title="<?php echo Lang::txt('COM_MEDIA_DIRECTORY_UP') ?>"><?php echo Lang::txt('COM_MEDIA_UP') ?></button>
		</div>
		<div class="fltrt">
			<button type="button" onclick="<?php if ($this->state->get('field.id')):?>window.parent.jInsertFieldValue($('#f_url').val(),'<?php echo $this->state->get('field.id');?>');<?php else:?>ImageManager.onok();<?php endif;?>window.parent.$.fancybox.close();"><?php echo Lang::txt('COM_MEDIA_INSERT') ?></button>
			<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('JCANCEL') ?></button>
		</div>
	</fieldset>

	<iframe id="imageframe" name="imageframe" src="<?php echo Route::url('index.php?option=com_media&view=imagesList&tmpl=component&folder=' . $this->state->folder . '&asset=' . Request::getCmd('asset') . '&author=' . Request::getCmd('author'));?>"></iframe>

	<fieldset>
		<table class="properties">
			<tr>
				<td><label for="f_url"><?php echo Lang::txt('COM_MEDIA_IMAGE_URL') ?></label></td>
				<td><input type="text" id="f_url" value="" /></td>
				<?php if (!$this->state->get('field.id')):?>
					<td><label for="f_align"><?php echo Lang::txt('COM_MEDIA_ALIGN') ?></label></td>
					<td>
						<select size="1" id="f_align" >
							<option value="" selected="selected"><?php echo Lang::txt('COM_MEDIA_NOT_SET') ?></option>
							<option value="left"><?php echo Lang::txt('JGLOBAL_LEFT') ?></option>
							<option value="right"><?php echo Lang::txt('JGLOBAL_RIGHT') ?></option>
						</select>
					</td>
					<td> <?php echo Lang::txt('COM_MEDIA_ALIGN_DESC');?> </td>
				<?php endif;?>
			</tr>
			<?php if (!$this->state->get('field.id')):?>
				<tr>
					<td><label for="f_alt"><?php echo Lang::txt('COM_MEDIA_IMAGE_DESCRIPTION') ?></label></td>
					<td><input type="text" id="f_alt" value="" /></td>
				</tr>
				<tr>
					<td><label for="f_title"><?php echo Lang::txt('COM_MEDIA_TITLE') ?></label></td>
					<td><input type="text" id="f_title" value="" /></td>
					<td><label for="f_caption"><?php echo Lang::txt('COM_MEDIA_CAPTION') ?></label></td>
					<td>
						<select size="1" id="f_caption" >
							<option value="" selected="selected" ><?php echo Lang::txt('JNO') ?></option>
							<option value="1"><?php echo Lang::txt('JYES') ?></option>
						</select>
					</td>
					<td> <?php echo Lang::txt('COM_MEDIA_CAPTION_DESC');?> </td>
				</tr>
			<?php endif;?>
		</table>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />
	</fieldset>
</form>

<?php if (User::authorise('core.create', 'com_media')): ?>
	<form action="<?php echo Request::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo Session::getFormToken();?>=1&amp;asset=<?php echo Request::getCmd('asset');?>&amp;author=<?php echo Request::getCmd('author');?>&amp;view=images" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
		<fieldset id="uploadform">
			<legend><?php echo $this->config->get('upload_maxsize')=='0' ? Lang::txt('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : Lang::txt('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
			<fieldset id="upload-noflash" class="actions">
				<label for="upload-file" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_UPLOAD_FILE'); ?></label>
				<input type="file" id="upload-file" name="Filedata[]" multiple="multiple" />
				<label for="upload-submit" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?></label>
				<input type="submit" id="upload-submit" value="<?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?>"/>
			</fieldset>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media&view=images&tmpl=component&fieldid='.Request::getCmd('fieldid', '').'&e_name='.Request::getCmd('e_name').'&asset='.Request::getCmd('asset').'&author='.Request::getCmd('author')); ?>" />
		</fieldset>
	</form>
<?php  endif; ?>
