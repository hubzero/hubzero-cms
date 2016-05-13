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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

$text = ($this->task == 'edit' ? Lang::txt('COM_STOREFRONT_EDIT') : Lang::txt('COM_STOREFRONT_NEW'));

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_PRODUCT') . ': ' . $text, 'storefront.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('category');

$this->css();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert("<?php echo 'Title cannot be empty' ?>");
	}
	else if (document.getElementById('field-pTagline').value == ''){
		alert("<?php echo 'Tagline cannot be empty' ?>");
	}
	else if (document.getElementById('field-description').value == ''){
		alert("<?php echo 'Description cannot be empty' ?>");
	}
	else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[pName]" id="field-title" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getName())); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-alais"><?php echo Lang::txt('Alias'); ?>:</label><br />
				<input type="text" name="fields[pAlias]" id="field-alais" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getAlias())); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-pTagline"><?php echo Lang::txt('COM_STOREFRONT_TAGLINE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[pTagline]" id="field-pTagline" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getTagline())); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo Lang::txt('COM_STOREFRONT_DESCRIPTION'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo $this->editor('fields[pDescription]', $this->escape(stripslashes($this->row->getDescription())), 50, 10, 'field-description', array('buttons' => false)); ?>
			</div>

			<div class="input-wrap">
				<label for="field-features"><?php echo Lang::txt('COM_STOREFRONT_FEATURES'); ?>:</label><br />
				<?php echo $this->editor('fields[pFeatures]', $this->escape(stripslashes($this->row->getFeatures())), 50, 10, 'field-features', array('buttons' => false)); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
					<td>
						<?php echo $this->row->getId(); ?>
						<input type="hidden" name="fields[pId]" id="field-id" value="<?php echo $this->escape($this->row->getId()); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_DOWNLOADED'); ?>:</th>
					<td>
						<?php
						echo $this->downloaded;
						if ($this->downloaded == 0 || $this->downloaded > 1)
						{
							echo(' times');
						}
						else {
							echo 'time';
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_TYPE'); ?>:</label>
				<select name="fields[ptId]" id="field-state">
					<?php

					foreach ($this->types as $type)
					{
						?>
						<option value="<?php echo $type->ptId; ?>"<?php if ($this->row->getType() == $type->ptId) { echo ' selected="selected"'; } ?>><?php echo $type->ptName; ?></option>
						<?php
					}

					?>
				</select>
			</div>

			<?php
			if ($this->metaNeeded) {
			?>
			<p>
				<a class="options-link" href="<?php echo 'index.php?option=' . $this->option . '&controller=meta&task=edit&id=' . $this->row->getId(); ?>">Edit type-related options (save product first if you updated the type)</a></p>
			<?php
			}
			?>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_ALLOW_MULTIPLE'); ?>:</label>
				<select name="fields[pAllowMultiple]" id="field-state">
					<option value="0"<?php if ($this->row->getAllowMultiple() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
					<option value="1"<?php if ($this->row->getAllowMultiple() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
				</select>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_STATE'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->getActiveStatus() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->getActiveStatus() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->getActiveStatus() == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-publish_up"><?php echo Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_UP'); ?>:</label><br />
				<?php echo Html::input('calendar', 'fields[publish_up]', ($this->row->getPublishTime()->publish_up != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->getPublishTime()->publish_up)->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-publish_up')); ?>
			</div>

			<div class="input-wrap">
				<label for="field-publish_down"><?php echo Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_DOWN'); ?>:</label><br />
				<?php echo Html::input('calendar', 'fields[publish_down]', ($this->row->getPublishTime()->publish_down != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->getPublishTime()->publish_down)->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-publish_down')); ?>
			</div>

			<div class="input-wrap">
				<label for="field-access"><?php echo Lang::txt('COM_STOREFRONT_ACCESS_LEVEL'); ?>:</label>
				<?php
				echo JHtml::_('access.level', 'fields[access]', $this->row->getAccessLevel());
				?>
			</div>
		</fieldset>

		<?php
		if ($this->collections->total())
		{
		?>
		<fieldset class="adminform">
			<legend><span><?php echo 'Collections'; ?></span></legend>

			<div class="input-wrap">
				<ul class="checklist catgories">
					<?php
					$collections = $this->row->getCollections();

					foreach ($this->collections as $cat)
					{
					?>
						<?php
						if ($cat->cActive || in_array($cat->cId, $collections))
						{
						?>
						<li>
							<input type="checkbox" name="fields[collections][]" <?php if (in_array($cat->cId, $collections)) { echo 'checked';} ?> value="<?php echo $cat->cId; ?>"
								   id="collection_<?php echo $cat->cId; ?>">
							<label for="collection_<?php echo $cat->cId; ?>">
								<?php echo $cat->cName; ?>
							</label>
						</li>
						<?php
						}
						?>
					<?php
					}
					?>
				</ul>
			</div>
		</fieldset>
		<?php
		}
		?>

		<?php
		if ($this->optionGroups->total())
		{
		?>

			<fieldset class="adminform">
				<legend><span><?php echo 'Product option groups'; ?></span></legend>

				<div class="input-wrap">
					<ul class="checklist optionGroups">
						<?php
						foreach ($this->optionGroups as $og)
						{
						?>
							<?php
							if ($og->ogActive || in_array($og->ogId, $this->productOptionGroups))
							{
							?>
							<li>
								<input type="checkbox"
									   name="fields[optionGroups][]" <?php if (in_array($og->ogId, $this->productOptionGroups)) {
									echo 'checked';
								} ?> value="<?php echo $og->ogId; ?>"
									   id="optionGroup_<?php echo $og->ogId; ?>">
								<label for="optionGroup_<?php echo $og->ogId; ?>">
									<?php echo $og->ogName; ?>
								</label>
							</li>
							<?php
							}
							?>
						<?php
						}
						?>
					</ul>
				</div>
			</fieldset>
		<?php
		}
		?>

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
					$img->imgId = NULL;
				}
				?>
				<div style="padding-top: 2.5em">
					<div id="ajax-uploader" data-action="index.php?option=<?php echo $this->option; ?>&amp;controller=images&amp;task=upload&amp;type=product&amp;id=<?php echo $this->row->getId(); ?>&amp;no_html=1&amp;<?php echo JUtility::getToken(); ?>=1">
						<noscript>
							<iframe height="350" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;controller=images&amp;tmpl=component&amp;file=<?php echo $file; ?>&amp;type=product&amp;id=<?php echo $this->row->getId(); ?>"></iframe>
						</noscript>
					</div>
				</div>
			<?php
			$width = 0;
			$height = 0;
			$this_size = 0;
			$pathl = DS . trim($this->config->get('imagesFolder', '/app/site/storefront/products'), DS) . DS . $this->row->getId();

			if ($image && file_exists(PATH_ROOT . $pathl . DS . $file))
			{
				$this_size = filesize(PATH_ROOT . $pathl . DS . $file);
				list($width, $height, $type, $attr) = getimagesize(PATH_ROOT . $pathl . DS . $file);
				$pic  = $file;
				$path = $pathl;
			}
			else
			{
				$pic = 'noimage.png';
				$path = dirname(dirname(dirname(dirname(str_replace(PATH_ROOT, '', __DIR__))))) . '/site/assets/img' . DS;
			}
			?>
				<div id="img-container">
					<img id="img-display" src="<?php echo $path . DS . $pic; ?>" alt="<?php echo Lang::txt('COM_STOREFRONT_PRODUCT_IMAGE'); ?>" />
					<input type="hidden" name="currentfile" id="currentfile" value="<?php echo $this->escape($image); ?>" />
				</div>

				<table class="formed">
					<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_STOREFRONT_FILE'); ?>:</th>
						<td>
							<span id="img-name"><?php echo $image; ?></span>
						</td>
						<td>
							<a id="img-delete" <?php echo $image ? '' : 'style="display: none;"'; ?>
							   href="index.php?option=<?php echo $this->option; ?>&amp;controller=images&amp;tmpl=component&amp;task=remove&amp;currentfile=<?php echo $img->imgId; ?>&amp;type=product&amp;id=<?php echo $this->row->getId(); ?>&amp;<?php echo JUtility::getToken(); ?>=1"
							   title="<?php echo Lang::txt('Delete'); ?>">[ x ]</a>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_STOREFRONT_PICTURE_SIZE'); ?>:</th>
						<td><span id="img-size"><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></span>
						</td>
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

				<script type="text/javascript" src="<?php echo rtrim(Request::root(true), '/'); ?>/core/assets/js/jquery.fileuploader.js"></script>
				<script type="text/javascript">
					String.prototype.nohtml = function () {
						if (this.indexOf('?') == -1) {
							return this + '?no_html=1';
						} else {
							return this + '&no_html=1';
						}
					};
					jQuery(document).ready(function($){
						if ($("#ajax-uploader").length) {
							var uploader = new qq.FileUploader({
								element: $("#ajax-uploader")[0],
								action: $("#ajax-uploader").attr("data-action"),
								multiple: true,
								debug: true,
								template: '<div class="qq-uploader">' +
								'<div class="qq-upload-button"><span><?php echo Lang::txt('COM_STOREFRONT_UPLOAD_CLICK_OR_DROP'); ?></span></div>' +
								'<div class="qq-upload-drop-area"><span><?php echo Lang::txt('COM_STOREFRONT_UPLOAD_CLICK_OR_DROP'); ?></span></div>' +
								'<ul class="qq-upload-list"></ul>' +
								'</div>',
								onComplete: function(id, file, response) {
									if (response.success) {
										$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
										$('#img-name').text(response.file);
										$('#img-size').text(response.size);
										$('#img-width').text(response.width);
										$('#img-height').text(response.height);

										$('#img-delete').show();
									}
								}
							});
						}
						$('#img-delete').on('click', function (e) {
							e.preventDefault();
							var el = $(this);
							$.getJSON(el.attr('href').nohtml(), {}, function(response) {
								if (response.success) {
									$('#img-display').attr('src', '<?php echo rtrim(Request::root(true), '/'); ?>/core/components/com_storefront/site/assets/img/noimage.png');
									$('#img-name').text('[ none ]');
									$('#img-size').text('0');
									$('#img-width').text('0');
									$('#img-height').text('0');
								}
								el.hide();
							});
						});
					});
				</script>
				<?php
			} else {
				echo '<p class="warning">'.Lang::txt('COM_STOREFRONT_PICTURE_ADDED_LATER').'</p>';
			}
			?>
		</fieldset>

	</div>
	<div class="clr"></div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
