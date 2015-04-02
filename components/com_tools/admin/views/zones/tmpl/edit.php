<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES') . ': ' . $text, 'tools.png');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('zone');

JHtml::_('behavior.modal');
JHtml::_('behavior.switcher', 'submenu');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu">
						<li><a href="#" onclick="return false;" id="profile" class="active"><?php echo Lang::txt('JDETAILS'); ?></a></li>
						<li><a href="#" onclick="return false;" id="locations"><?php echo Lang::txt('COM_TOOLS_FIELDSET_LOCATIONS'); ?></a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="zone-document">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>
		<div id="page-profile" class="tab">
			<div class="col width-60 fltlft">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

					<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="save" />

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_HINT'); ?>">
						<label for="field-zone"><?php echo Lang::txt('COM_TOOLS_FIELD_ZONE'); ?>:</label>
						<input type="text" name="fields[zone]" id="field-zone" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('zone'))); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_HINT'); ?></span>
					</div>

					<div class="input-wrap">
						<label for="field-zone"><?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>:</label>
						<input type="text" name="fields[title]" id="field-title" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
					</div>

					<div class="input-wrap">
						<label for="field-description"><?php echo Lang::txt('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label>
						<textarea name="fields[description]" id="field-description" cols="35" rows="2"><?php echo $this->escape(stripslashes($this->row->get('description'))); ?></textarea>
					</div>

					<div class="input-wrap">
						<label for="field-master"><?php echo Lang::txt('COM_TOOLS_FIELD_MASTER'); ?>:</label>
						<input type="text" name="fields[master]" id="field-master" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('master'))); ?>" />
					</div>

					<div class="input-wrap">
						<label for="field-type"><?php echo Lang::txt('COM_TOOLS_FIELD_TYPE'); ?>:</label>
						<select name="fields[type]" id="field-type">
							<option value="local"<?php if ($this->row->get('type') == 'local') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_FIELD_TYPE_LOCAL'); ?></option>
							<option value="remote"<?php if ($this->row->get('type') == 'remote') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_FIELD_TYPE_REMOTE'); ?></option>
						</select>
					</div>

					<div class="input-wrap">
						<?php echo Lang::txt('COM_TOOLS_FIELD_STATE'); ?>:<br />
						<label for="field-state-up"><input class="option" type="radio" name="fields[state]" id="field-state-up" size="30" value="up"<?php if ($this->row->get('state') == 'up') { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_TOOLS_FIELD_STATE_UP'); ?></label>
						<label for="field-state-down"><input class="option" type="radio" name="fields[state]" id="field-state-down" size="30" value="down"<?php if ($this->row->get('state') == 'down') { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_TOOLS_FIELD_STATE_DOWN'); ?></label>
					</div>
				</fieldset>
			</div>
			<div class="col width-40 fltrt">
				<table class="meta">
					<tbody>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_ID'); ?></th>
							<td><?php echo $this->escape($this->row->get('id')); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_STATE'); ?></th>
							<td><?php echo $this->escape($this->row->get('state')); ?></td>
						</tr>
					</tbody>
				</table>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_IMAGE'); ?></span></legend>

					<?php
					if ($this->row->exists())
					{
						$this->css('fileupload.css')
						     ->js('jquery.fileuploader.js', 'system');
					?>
					<div style="padding-top: 2.5em">
						<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=upload&id=' . $this->row->get('id') . '&no_html=1&' . JUtility::getToken() . '=1'); ?>">
							<noscript>
								<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&id=' . $this->row->get('id')); ?>"></iframe>
							</noscript>
						</div>
					</div>
						<?php
						$width = 0;
						$height = 0;
						$this_size = 0;
						if ($pic = $this->row->get('picture'))
						{
							$path = $this->row->logo('path');

							$this_size = filesize($path . DS . $pic);
							list($width, $height, $type, $attr) = getimagesize($path . DS . $pic);
						}
						else
						{
							$pic  = 'blank.png';
							$path = '/components/com_tools/admin/assets/img';
						}
						?>
						<table class="formed">
							<tbody>
								<tr>
									<td rowspan="6">
										<img id="img-display" src="<?php echo '..' . str_replace(JPATH_ROOT, '', $path) . DS . $pic; ?>" alt="<?php echo Lang::txt('COM_TOOLS_FIELDSET_IMAGE'); ?>" />
									</td>
									<td><?php echo Lang::txt('COM_TOOLS_IMAGE_FILE'); ?>:</td>
									<td><span id="img-name"><?php echo $this->row->get('picture', Lang::txt('COM_TOOLS_IMAGE_NONE')); ?></span></td>
								</tr>
								<tr>
									<td><?php echo Lang::txt('COM_TOOLS_IMAGE_SIZE'); ?>:</td>
									<td><span id="img-size"><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></span></td>
								</tr>
								<tr>
									<td><?php echo Lang::txt('COM_TOOLS_IMAGE_WIDTH'); ?>:</td>
									<td><span id="img-width"><?php echo $width; ?></span> px</td>
								</tr>
								<tr>
									<td><?php echo Lang::txt('COM_TOOLS_IMAGE_HEIGHT'); ?>:</td>
									<td><span id="img-height"><?php echo $height; ?></span> px</td>
								</tr>
								<tr>
									<td><input type="hidden" name="currentfile" id="currentfile" value="<?php echo $file; ?>" /></td>
									<td><a id="img-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=removefile&id=' . $this->row->get('id') . '&' . JUtility::getToken() . '=1'); ?>">[ <?php echo Lang::txt('DELETE'); ?> ]</a></td>
								</tr>
							</tbody>
						</table>

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
									action: $("#ajax-uploader").attr("data-action"), // + $('#field-dir').val()
									//params: {listdir: $('#listdir').val()},
									multiple: true,
									debug: true,
									template: '<div class="qq-uploader">' +
												'<div class="qq-upload-button"><span><?php echo Lang::txt('COM_TOOLS_IMAGE_CLICK_OR_DROP'); ?></span></div>' +
												'<div class="qq-upload-drop-area"><span><?php echo Lang::txt('COM_TOOLS_IMAGE_CLICK_OR_DROP'); ?></span></div>' +
												'<ul class="qq-upload-list"></ul>' +
											   '</div>',
									/*onSubmit: function(id, file) {
										//$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
									},*/
									onComplete: function(id, file, response) {
										if (response.success) {
											$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
											$('#img-name').text(response.file);
											$('#img-size').text(response.size);
											$('#img-width').text(response.width);
											$('#img-height').text(response.height);

											$('#img-delete').show();
										//$('#imgManager').attr('src', $('#imgManager').attr('src'));
										}
									}
								});
							}
							$('#img-delete').on('click', function (e) {
								e.preventDefault();
								var el = $(this);
								$.getJSON(el.attr('href').nohtml(), {}, function(response) {
									if (response.success) {
										$('#img-display').attr('src', '../media/images/blank.png');
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
						echo '<p class="warning">' . Lang::txt('COM_TOOLS_PICTURE_ADDED_LATER') . '</p>';
					}
					?>
				</fieldset>
			</div>
			<div class="clr"></div>
		</div>

		<div id="page-locations" class="tab">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_LOCATIONS'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<iframe width="100%" height="400" name="locationslist" id="locationslist" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=locations&tmpl=component&zone=' . $this->row->get('id')); ?>"></iframe>
			<?php } else { ?>
				<p><?php echo Lang::txt('COM_TOOLS_LOCATIONS_ADDED_LATER'); ?></p>
			<?php } ?>
			</fieldset>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
</form>
