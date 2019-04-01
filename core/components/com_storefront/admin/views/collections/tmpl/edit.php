<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

$text = ($this->task == 'edit' ? Lang::txt('COM_STOREFRONT_EDIT') : Lang::txt('COM_STOREFRONT_NEW'));

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_COLLECTION') . ': ' . $text, 'storefront.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
//Toolbar::spacer();
//Toolbar::help('category');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->css()
	->js('jquery.fileuploader.js', 'system')
	->js();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_STOREFRONT_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[cName]" id="field-title" class="required" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getName())); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('Alias'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[alias]" id="field-alias" class="required" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getAlias())); ?>" />
				</div>

			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
						<td>
							<?php echo $this->row->getId(); ?>
							<input type="hidden" name="fields[cId]" id="field-id" value="<?php echo $this->escape($this->row->getId()); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_PUBLISH'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->getActiveStatus() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->getActiveStatus() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					</select>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('Image'); ?></span></legend>

				<?php
				if ($this->row->getId()) {

					$img = $this->row->getImage();

					if (!empty($img))
					{
						$image = stripslashes($img->imgName);
						$pics = explode(DS, $image);
						$file = end($pics);
					}
					else {
						$image = false;
						$file = false;
						$img = new \stdClass();
						$img->imgId = null;
					}
					?>
					<div class="uploader-wrap">
						<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=images&task=upload&type=collection&id=' . $this->row->getId() . '&no_html=1&' . Session::getFormToken() . '=1'); ?>" data-instructions="<?php echo Lang::txt('COM_STOREFRONT_UPLOAD_CLICK_OR_DROP'); ?>">
							<noscript>
								<iframe height="350" name="filer" id="filer" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=images&tmpl=component&file=' . $file . '&type=collection&id=' . $this->row->getId()); ?>"></iframe>
							</noscript>
						</div>
					</div>
				<?php
				$width = 0;
				$height = 0;
				$this_size = 0;
				$pathl = DS . trim($this->config->get('collectionsImagesFolder', '/site/storefront/collections'), DS) . DS . $this->row->getId();

				if ($image && file_exists(PATH_APP . $pathl . DS . $file))
				{
					$this_size = filesize(PATH_APP . $pathl . DS . $file);
					list($width, $height, $type, $attr) = getimagesize(PATH_APP . $pathl . DS . $file);
					$pic  = $file;
					$path = '/app/' . $pathl;
				}
				else
				{
					$image = false;
					$pic = 'noimage.png';
					$path = dirname(dirname(dirname(dirname(str_replace(PATH_ROOT, '', __DIR__))))) . '/site/assets/img';
				}
				?>
					<div id="img-container">
						<img id="img-display" src="<?php echo $path . DS . $pic; ?>" alt="<?php echo Lang::txt('COM_STOREFRONT_PRODUCT_IMAGE'); ?>" />
						<input type="hidden" name="currentfile" id="currentfile" value="<?php echo $img->imgId; ?>" />
					</div>

					<table class="formed">
						<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_STOREFRONT_FILE'); ?>:</th>
							<td>
								<span id="img-name"><?php echo $image; ?></span>
							</td>
							<td>
								<a id="img-delete <?php echo $image ? '' : 'hide'; ?>"
								   href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=images&tmpl=component&task=remove&currentfile=' . $img->imgId . '&type=collection&id=' . $this->row->getId() . '&' . Session::getFormToken() . '=1'); ?>"
								   title="<?php echo Lang::txt('Delete'); ?>"
								   data-noimg="/core/components/com_storefront/site/assets/img/noimage.png">[ x ]</a>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_STOREFRONT_PICTURE_SIZE'); ?>:</th>
							<td><span id="img-size"><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></span></td>
							<td></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_STOREFRONT_PICTURE_WIDTH'); ?>:</th>
							<td><span id="img-width"><?php echo $width; ?></span> px</td>
							<td></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_STOREFRONT_PICTURE_HEIGHT'); ?>:</th>
							<td><span id="img-height"><?php echo $height; ?></span> px</td>
							<td></td>
						</tr>
						</tbody>
					</table>
					<?php
				} else {
					echo '<p class="warning">'.Lang::txt('COM_STOREFRONT_PICTURE_ADDED_LATER').'</p>';
				}
				?>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
