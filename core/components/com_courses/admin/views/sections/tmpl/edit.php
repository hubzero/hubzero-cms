<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_SECTIONS') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();


Html::behavior('switcher', 'submenu');
Html::behavior('calendar');

$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));

$this->css(); //->css('classic');

$course_id = 0;
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
jQuery(document).ready(function($){
	$('#section-document').tabs();
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">

	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="coursesection">
						<li><a href="#" onclick="return false;" id="details" class="active"><?php echo Lang::txt('JDETAILS'); ?></a></li>
						<li><a href="#" onclick="return false;" id="managers"><?php echo Lang::txt('COM_COURSES_FIELDSET_MANAGERS'); ?></a></li>
						<li><a href="#" onclick="return false;" id="datetime"><?php echo Lang::txt('COM_COURSES_FIELDSET_DATES'); ?></a></li>
						<li><a href="#" onclick="return false;" id="badge"><?php echo Lang::txt('COM_COURSES_FIELDSET_REWARDS'); ?></a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="section-document">
		<div id="page-details" class="tab">
			<div class="grid">
				<div class="col span6">
					<fieldset class="adminform">
						<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

						<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->row->get('offering_id'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="task" value="save" />

						<div class="input-wrap">
							<label for="offering_id"><?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:</label><br />
							<select name="fields[offering_id]" id="offering_id">
								<option value="-1"><?php echo Lang::txt('COM_COURSES_SELECT'); ?></option>
								<?php
									require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
									$model = \Components\Courses\Models\Courses::getInstance();
									if ($model->courses()->total() > 0)
									{
										foreach ($model->courses() as $course)
										{
								?>
											<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
								<?php
											$j = 0;
											foreach ($course->offerings() as $i => $offering)
											{
												if ($offering->get('id') == $this->row->get('offering_id'))
												{
													$course_id = $offering->get('course_id');
												}
								?>
												<option value="<?php echo $this->escape(stripslashes($offering->get('id'))); ?>"<?php if ($offering->get('id') == $this->row->get('offering_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('alias'))); ?></option>
								<?php
											}
								?>
											</optgroup>
								<?php
										}
									}
								?>
							</select>
						</div>

						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
							<label for="field-alias"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
							<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
						</div>

						<div class="input-wrap">
							<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>:</label><br />
							<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
						</div>

						<fieldset>
							<legend><?php echo Lang::txt('COM_COURSES_FIELD_DEFAULT_SECTION'); ?>:</legend>
							<div class="input-wrap">
								<label for="field-is_default-yes"><input type="radio" name="fields[is_default]" id="field-is_default-yes" value="1" <?php if ($this->row->get('is_default', 0) == 1) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('JYES'); ?></label>
								<label for="field-is_default-no"><input type="radio" name="fields[is_default]" id="field-is_default-no" value="0" <?php if ($this->row->get('is_default', 0) == 0) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('JNO'); ?></label>
							</div>
						</fieldset>

						<div class="input-wrap">
							<label for="field-enrollment"><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT'); ?>:</label><br />
							<select name="fields[enrollment]" id="field-enrollment">
								<option value="0"<?php if ($this->row->get('enrollment', $this->row->config('default_enrollment', 0)) == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT_OPEN'); ?></option>
								<option value="1"<?php if ($this->row->get('enrollment', $this->row->config('default_enrollment', 0)) == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT_RESTRICTED'); ?></option>
								<option value="2"<?php if ($this->row->get('enrollment', $this->row->config('default_enrollment', 0)) == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT_CLOSED'); ?></option>
							</select>
						</div>

						<div class="input-wrap">
							<label for="field-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label><br />
							<select name="fields[state]" id="field-state">
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
								<option value="3"<?php if ($this->row->get('state') == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></option>
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
							</select>
						</div>
					</fieldset>

					<fieldset class="adminform">
						<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_PUBLISH_UP_HINT'); ?>">
							<label for="field-publish_up"><?php echo Lang::txt('COM_COURSES_FIELD_PUBLISH_UP'); ?>:</label><br />
							<?php echo Html::input('calendar', 'fields[publish_up]', ($this->row->get('publish_up') != '0000-00-00 00:00:00' ? $this->row->get('publish_up') : ''), array('id' => 'field-publish_up')); ?>
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_PUBLISH_UP_HINT'); ?></span>
						</div>

						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_SECTION_STARTS_HINT'); ?>">
							<label for="field-start_date"><?php echo Lang::txt('COM_COURSES_FIELD_SECTION_STARTS'); ?>:</label><br />
							<?php echo Html::input('calendar', 'fields[start_date]', ($this->row->get('start_date') != '0000-00-00 00:00:00' ? $this->row->get('start_date') : ''), array('id' => 'field-start_date')); ?>
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_SECTION_STARTS_HINT'); ?></span>
						</div>

						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_FINISHES_HINT'); ?>">
							<label for="field-end date"><?php echo Lang::txt('COM_COURSES_FIELD_FINISHES'); ?>:</label><br />
							<?php echo Html::input('calendar', 'fields[end_date]', ($this->row->get('end_date') != '0000-00-00 00:00:00' ? $this->row->get('end_date') : ''), array('id' => 'field-end_date')); ?>
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_FINISHES_HINT'); ?></span>
						</div>

						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN_HINT'); ?>">
							<label for="field-publish_down"><?php echo Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN'); ?>:</label><br />
							<?php echo Html::input('calendar', 'fields[publish_down]', ($this->row->get('publish_down') != '0000-00-00 00:00:00' ? $this->row->get('publish_down') : ''), array('id' => 'field-publish_down')); ?>
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN_HINT'); ?></span>
						</div>
					</fieldset>
				</div>
				<div class="col span6">
					<table class="meta">
						<tbody>
							<tr>
								<th><?php echo Lang::txt('COM_COURSES_FIELD_COURSE_ID'); ?></th>
								<td colspan="3"><?php echo $this->escape($course_id); ?></td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_COURSES_FIELD_OFFERING_ID'); ?></th>
								<td colspan="3"><?php echo $this->escape($this->row->get('offering_id')); ?></td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_COURSES_FIELD_SECTION_ID'); ?></th>
								<td colspan="3"><?php echo $this->escape($this->row->get('id')); ?></td>
							</tr>
							<?php if ($this->row->get('created')) { ?>
								<tr>
									<th><?php echo Lang::txt('COM_COURSES_FIELD_CREATED'); ?></th>
									<td>
										<?php echo $this->escape($this->row->get('created')); ?>
									</td>
								</tr>
								<?php if ($this->row->get('created_by')) { ?>
									<tr>
										<th><?php echo Lang::txt('COM_COURSES_FIELD_CREATOR'); ?></th>
										<td><?php
											$creator = User::getInstance($this->row->get('created_by'));
											echo $this->escape(stripslashes($creator->get('name'))); ?>
										</td>
									</tr>
								<?php } ?>
							<?php } ?>
						</tbody>
					</table>

					<fieldset class="adminform">
						<legend><span><?php echo Lang::txt('COM_COURSES_LOGO'); ?></span></legend>

						<?php
						if ($this->row->exists())
						{
							$logo = $this->row->params('logo');
							?>
							<div style="padding-top: 2.5em">
								<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=logo&task=upload&type=section&id=' . $this->row->get('id') . '&no_html=1&' . Session::getFormToken() . '=1'); ?>">
									<noscript>
										<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=logo&tmpl=component&file=' . $logo . '&type=section&id=' . $this->row->get('id')); ?>"></iframe>
									</noscript>
								</div>
							</div>
								<?php
								$width  = 0;
								$height = 0;
								$this_size = 0;
								if ($logo)
								{
									$path = $this->row->logo('path');

									$this_size = filesize(PATH_APP . $path . DS . $logo);
									list($width, $height, $type, $attr) = getimagesize(PATH_APP . $path . DS . $logo);
									$pic = $logo;
								}
								else
								{
									$pic  = 'blank.png';
									$path = '/core/components/com_courses/admin/assets/img';
								}
								?>
								<div id="img-container">
									<img id="img-display" src="<?php echo '..' . $path . DS . $pic; ?>" alt="<?php echo Lang::txt('COM_COURSES_LOGO'); ?>" />
									<input type="hidden" name="currentfile" id="currentfile" value="<?php echo $this->escape($logo); ?>" />
								</div>
								<table class="formed">
									<tbody>
										<tr>
											<th><?php echo Lang::txt('COM_COURSES_FILE'); ?>:</th>
											<td>
												<span id="img-name"><?php echo $this->row->params('logo', Lang::txt('COM_COURSES_NONE')); ?></span>
											</td>
											<td>
												<a id="img-delete" <?php echo $logo ? '' : 'style="display: none;"'; ?> href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=logo&tmpl=component&task=remove&currentfile=' . $logo . '&type=section&id=' . $this->row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>">[ x ]</a>
											</td>
										</tr>
										<tr>
											<th><?php echo Lang::txt('COM_COURSES_PICTURE_SIZE'); ?>:</th>
											<td><span id="img-size"><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></span></td>
											<td></td>
										</tr>
										<tr>
											<th><?php echo Lang::txt('COM_COURSES_PICTURE_WIDTH'); ?>:</th>
											<td><span id="img-width"><?php echo $width; ?></span> px</td>
											<td></td>
										</tr>
										<tr>
											<th><?php echo Lang::txt('COM_COURSES_PICTURE_HEIGHT'); ?>:</th>
											<td><span id="img-height"><?php echo $height; ?></span> px</td>
											<td></td>
										</tr>
									</tbody>
								</table>

								<script type="text/javascript" src="<?php echo $base; ?>/core/assets/js/jquery.fileuploader.js"></script>
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
														'<div class="qq-upload-button"><span><?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?></span></div>' +
														'<div class="qq-upload-drop-area"><span><?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?></span></div>' +
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
												$('#img-display').attr('src', '../core/components/com_courses/admin/assets/img/blank.png');
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
							echo '<p class="warning">' . Lang::txt('COM_COURSES_UPLOAD_ADDED_LATER') . '</p>';
						}
						?>
					</fieldset>

					<?php $params = new \Hubzero\Config\Registry($this->row->get('params')); ?>

					<fieldset class="adminform sectionparams">
						<legend><?php echo Lang::txt('COM_COURSES_FIELDSET_PARAMS'); ?></legend>
						<div class="input-wrap">
							<label for="params-progress-calculation"><?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION'); ?>:</label><br />
							<select name="params[progress_calculation]" id="params-progress-calculation">
								<option value=""<?php echo ($params->get('progress_calculation', '') == '') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_INHERIT_FROM_OFFERING'); ?></option>
								<option value="all"<?php echo ($params->get('progress_calculation', '') == 'all') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_ALL'); ?></option>
								<option value="graded"<?php echo ($params->get('progress_calculation', '') == 'graded') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_GRADED'); ?></option>
								<option value="videos"<?php echo ($params->get('progress_calculation', '') == 'videos') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_VIDEOS'); ?></option>
								<option value="manual"<?php echo ($params->get('progress_calculation', '') == 'manual') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_MANUAL'); ?></option>
							</select>
						</div>
						<div class="input-wrap">
							<label for="params-progress-calculation"><?php echo Lang::txt('COM_COURSES_PREVIEW_MODE'); ?>:</label><br />
							<select name="params[preview]" id="params-preview">
								<option value="0"<?php echo ($params->get('preview', '') == '0') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PREVIEW_NO'); ?></option>
								<option value="1"<?php echo ($params->get('preview', '') == '1') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PREVIEW_YES_FULL'); ?></option>
								<option value="2"<?php echo ($params->get('preview', '') == '2') ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_PREVIEW_YES_FIRST_UNIT'); ?></option>
							</select>
						</div>
					</fieldset>

					<?php
						if ($plugins = Event::trigger('courses.onSectionEdit'))
						{
							$data = $this->row->get('params');

							foreach ($plugins as $plugin)
							{
								$param = new \Hubzero\Html\Parameter(
									(is_object($data) ? $data->toString() : $data),
									PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
								);
								$out = $param->render('params', 'onSectionEdit');
								if (!$out)
								{
									continue;
								}
								?>
								<fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
									<legend><?php echo Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']); ?></legend>
									<div class="input-wrap">
										<?php echo $out; ?>
									</div>
								</fieldset>
								<?php
							}
						}
					?>
				</div>
			</div>
		</div>

		<div id="page-managers" class="tab">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_MANAGERS'); ?></span></legend>
				<?php if ($this->row->get('id')) { ?>
					<iframe width="100%" height="500" name="managers" id="managers" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=supervisors&tmpl=component&offering=' . $this->row->get('offering_id') . '&section=' . $this->row->get('id')); ?>"></iframe>
				<?php } else { ?>
					<p class="warning"><?php echo Lang::txt('COM_COURSES_FIELDSET_MANAGERS_WARNING'); ?></p>
				<?php } ?>
			</fieldset>
		</div>

		<div id="page-datetime" class="tab">
		<?php if ($this->offering->units()->total() > 0) { ?>
			<div class="col width-100">
				<?php if (!$this->row->exists() && !$this->row->get('is_default')) { ?>
				<p class="info"><?php echo Lang::txt('COM_COURSES_SECTION_DATES_HELP'); ?></p>
				<?php } ?>

				<?php
				echo Html::sliders('start', 'content-pane');

				$nullDate = '0000-00-00 00:00:00';

				$this->offering->section($this->row->get('alias', '!!default!!'));

					$i = 0;
					foreach ($this->offering->units(array(), true) as $unit)
					{
						echo Html::sliders('panel', stripslashes($unit->get('title')), stripslashes($unit->get('alias')));
						?>
							<input type="hidden" name="dates[<?php echo $i; ?>][id]" value="<?php echo $this->row->date('unit', $unit->get('id'))->get('id'); ?>" />
							<input type="hidden" name="dates[<?php echo $i; ?>][scope]" value="unit" />
							<input type="hidden" name="dates[<?php echo $i; ?>][scope_id]" value="<?php echo $unit->get('id'); ?>" />

							<table class="admintable section-dates" id="dates_<?php echo $i; ?>">
								<tbody>
									<tr>
										<th class="key"><label for="dates_<?php echo $i; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_FROM'); ?></label></th>
										<td>
											<?php $tm = ($unit->get('publish_up') && $unit->get('publish_up') != $nullDate ? $unit->get('publish_up') : $this->row->date('unit', $unit->get('id'))->get('publish_up')); ?>
											<input type="text" name="dates[<?php echo $i; ?>][publish_up]" id="dates_<?php echo $i; ?>_publish_up" class="datetime-field" value="<?php echo (!$tm || $tm == $nullDate ? '' : Date::of($tm)->toLocal('Y-m-d H:i:s')); ?>" />
										</td>
										<th class="key"><label for="dates_<?php echo $i; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_TO'); ?></label></th>
										<td>
											<?php $tm = ($unit->get('publish_down') && $unit->get('publish_down') != $nullDate ? $unit->get('publish_down') : $this->row->date('unit', $unit->get('id'))->get('publish_down')); ?>
											<input type="text" name="dates[<?php echo $i; ?>][publish_down]" id="dates_<?php echo $i; ?>_publish_down" class="datetime-field" value="<?php echo (!$tm || $tm == $nullDate ? '' : Date::of($tm)->toLocal('Y-m-d H:i:s')); ?>" />
										</td>
										<td>
											<?php echo Lang::txt('COM_COURSES_SECTION_DATES_INHERITED'); ?>
										</td>
									</tr>
								</tbody>
							</table>
							<table class="admintable section-dates" id="dates_<?php echo $i; ?>">
								<tbody>
							<?php
							// Loop through the asset group types
							$z = 0;
							foreach ($unit->assetgroups() as $agt)
							{
								$agt->set('publish_up', $this->row->date('asset_group', $agt->get('id'))->get('publish_up'));
								$agt->set('publish_down', $this->row->date('asset_group', $agt->get('id'))->get('publish_down'));

								if ($agt->get('publish_up') == $nullDate)
								{
									$agt->set('publish_up', $unit->get('publish_up'));
								}
								if ($agt->get('publish_down') == $nullDate)
								{
									$agt->set('publish_down', $unit->get('publish_down'));
								}
								?>

									<tr>
										<th class="key">
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp;
											<?php echo $this->escape(stripslashes($agt->get('title'))); ?>
										</th>
										<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_FROM'); ?></label></th>
										<td>
											<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][id]" value="<?php echo $this->row->date('asset_group', $agt->get('id'))->get('id'); ?>" />
											<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][scope]" value="asset_group" />
											<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][scope_id]" value="<?php echo $agt->get('id'); ?>" />
											<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_up" class="datetime-field" value="<?php echo (!$agt->get('publish_up') || $agt->get('publish_up') == $unit->get('publish_up') || $agt->get('publish_up') == $nullDate ? '' : Date::of($agt->get('publish_up'))->toLocal('Y-m-d H:i:s')); ?>" />
										</td>
										<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_TO'); ?></label></th>
										<td>
											<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_down" class="datetime-field" value="<?php echo (!$agt->get('publish_down') || $agt->get('publish_down') == $unit->get('publish_down') || $agt->get('publish_down') == $nullDate ? '' : Date::of($agt->get('publish_down'))->toLocal('Y-m-d H:i:s')); ?>" />
										</td>
									</tr>

								<?php
								$j = 0;
								foreach ($agt->children() as $ag)
								{
									$ag->set('publish_up', $this->row->date('asset_group', $ag->get('id'))->get('publish_up'));
									$ag->set('publish_down', $this->row->date('asset_group', $ag->get('id'))->get('publish_down'));

									if ($ag->get('publish_up') == $nullDate)
									{
										$ag->set('publish_up', $agt->get('publish_up'));
									}
									if ($ag->get('publish_down') == $nullDate)
									{
										$ag->set('publish_down', $agt->get('publish_down'));
									}
									?>
											<tr>
												<th class="key">
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp;
													<?php echo $this->escape(stripslashes($ag->get('title'))); ?>
												</th>
												<td><label for="dates_<?php echo $i; ?>_<?php echo $j; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_FROM'); ?></label></th>
												<td>
													<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][id]" value="<?php echo $this->row->date('asset_group', $ag->get('id'))->get('id'); ?>" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][scope]" value="asset_group" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][scope_id]" value="<?php echo $ag->get('id'); ?>" />
													<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_up" class="datetime-field" value="<?php echo (!$ag->get('publish_up') || $ag->get('publish_up') == $agt->get('publish_up') || $ag->get('publish_up') == $nullDate ? '' : Date::of($ag->get('publish_up'))->toLocal('Y-m-d H:i:s')); ?>" />
												</td>
												<td><label for="dates_<?php echo $i; ?>_<?php echo $j; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_TO'); ?></label></th>
												<td>
													<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_down" class="datetime-field" value="<?php echo (!$ag->get('publish_down') || $ag->get('publish_down') == $agt->get('publish_down') || $ag->get('publish_down') == $nullDate ? '' : Date::of($ag->get('publish_down'))->toLocal('Y-m-d H:i:s')); ?>" />
												</td>
											</tr>

									<?php

									if ($ag->assets()->total())
									{
										$k = 0;
										foreach ($ag->assets() as $a)
										{
											$a->set('publish_up', $this->row->date('asset', $a->get('id'))->get('publish_up'));
											$a->set('publish_down', $this->row->date('asset', $a->get('id'))->get('publish_down'));

											if ($a->get('publish_up') == $nullDate)
											{
												$a->set('publish_up', $ag->get('publish_up'));
											}
											if ($a->get('publish_down') == $nullDate)
											{
												$a->set('publish_down', $ag->get('publish_down'));
											}
											?>
													<tr>
														<th class="key">
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp;
															<?php echo $this->escape(stripslashes($a->get('title'))); ?>
														</th>
														<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_FROM'); ?></label></th>
														<td>
															<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][id]" value="<?php echo $this->row->date('asset', $a->get('id'))->get('id'); ?>" />
															<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][scope]" value="asset" />
															<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][scope_id]" value="<?php echo $a->get('id'); ?>" />
															<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_up" class="datetime-field" value="<?php echo (!$a->get('publish_up') || $a->get('publish_up') == $ag->get('publish_up') || $a->get('publish_up') == $nullDate ? '' : Date::of($a->get('publish_up'))->toLocal('Y-m-d H:i:s')); ?>" />
														</td>
														<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_TO'); ?></label></th>
														<td>
															<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_down" class="datetime-field" value="<?php echo (!$a->get('publish_down') || $a->get('publish_down') == $ag->get('publish_down') || $a->get('publish_down') == $nullDate ? '' : Date::of($a->get('publish_down'))->toLocal('Y-m-d H:i:s')); ?>" />
														</td>
													</tr>

											<?php
											$k++;
										}
									}
									$j++;
								}
								if ($agt->assets()->total())
								{
									$k = 0;
									foreach ($agt->assets() as $a)
									{
										$a->set('publish_up', $this->row->date('asset', $a->get('id'))->get('publish_up'));
										$a->set('publish_down', $this->row->date('asset', $a->get('id'))->get('publish_down'));

										if ($a->get('publish_up') == $nullDate)
										{
											$a->set('publish_up', $agt->get('publish_up'));
										}
										if ($a->get('publish_down') == $nullDate)
										{
											$a->set('publish_down', $agt->get('publish_down'));
										}
										?>
												<tr>
													<th class="key">
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp;
														<?php echo $this->escape(stripslashes($a->get('title'))); ?>
													</th>
													<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_FROM'); ?></label></th>
													<td>
														<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][id]" value="<?php echo $this->row->date('asset', $a->get('id'))->get('id'); ?>" />
														<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][scope]" value="asset" />
														<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][scope_id]" value="<?php echo $a->get('id'); ?>" />
														<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_up" class="datetime-field" value="<?php echo (!$a->get('publish_up') || $a->get('publish_up') == $agt->get('publish_up') || $a->get('publish_up') == $nullDate ? '' : Date::of($a->get('publish_up'))->toLocal('Y-m-d H:i:s')); ?>" />
													</td>
													<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_TO'); ?></label></th>
													<td>
														<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_down" class="datetime-field" value="<?php echo (!$a->get('publish_down') || $a->get('publish_down') == $agt->get('publish_down') || $a->get('publish_down') == $nullDate ? '' : Date::of($a->get('publish_down'))->toLocal('Y-m-d H:i:s')); ?>" />
													</td>
												</tr>

										<?php
										$k++;
									}
								}
								$z++;
							}
							if ($unit->assets()->total())
							{
								$k = 0;
								foreach ($unit->assets() as $a)
								{
									$a->set('publish_up', $this->row->date('asset', $a->get('id'))->get('publish_up'));
									$a->set('publish_down', $this->row->date('asset', $a->get('id'))->get('publish_down'));

									if ($a->get('publish_up') == $nullDate)
									{
										$a->set('publish_up', $unit->get('publish_up'));
									}
									if ($a->get('publish_down') == $nullDate)
									{
										$a->set('publish_down', $unit->get('publish_down'));
									}
									?>
											<tr>
												<th class="key">
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp;
													<?php echo $this->escape(stripslashes($a->get('title'))); ?>
												</th>
												<td><label for="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_FROM'); ?></label></th>
												<td>
													<input type="hidden" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][id]" value="<?php echo $this->row->date('asset', $a->get('id'))->get('id'); ?>" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][scope]" value="asset" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][scope_id]" value="<?php echo $a->get('id'); ?>" />
													<input type="text" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][publish_up]" id="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_up" class="datetime-field" value="<?php echo (!$a->get('publish_up') || $a->get('publish_up') == $unit->get('publish_up') || $a->get('publish_up') == $nullDate ? '' : Date::of($a->get('publish_up'))->toLocal('Y-m-d H:i:s')); ?>" />
												</td>
												<td><label for="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo Lang::txt('COM_COURSES_TO'); ?></label></th>
												<td>
													<input type="text" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][publish_down]" id="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_down" class="datetime-field" value="<?php echo (!$a->get('publish_down') || $a->get('publish_down') == $unit->get('publish_down') || $a->get('publish_down') == $nullDate ? '' : Date::of($a->get('publish_down'))->toLocal('Y-m-d H:i:s')); ?>" />
												</td>
											</tr>

									<?php
									$k++;
								}
							}
							?>
								</tbody>
							</table>
						<?php
						$i++;
					}

				echo Html::sliders('end');
				?>
				<!-- </fieldset> -->
				<script type="text/javascript">
				jQuery(document).ready(function($){
					$('.datetime-field').datetimepicker({
						duration: '',
						showTime: true,
						constrainInput: false,
						stepMinutes: 1,
						stepHours: 1,
						altTimeField: '',
						time24h: true,
						dateFormat: 'yy-mm-dd',
						timeFormat: 'HH:mm:00'
					});
				});
				</script>
			</div>
			<div class="clr"></div>
		<?php } else { ?>
			<p class="warning"><?php echo Lang::txt('COM_COURSES_NO_DATES_FOUND'); ?></p>
		<?php } ?>
		</div>
		<div id="page-badge" class="tab">
			<?php $certificate = $this->course->certificate();
			if ($certificate->exists() && $certificate->hasFile()) { ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_CERTIFICATE'); ?></span></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_EXPLANATION'); ?>">
						<label for="params-certificate"><?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE'); ?>:</label><br />
						<select name="params[certificate]" id="params-certificate">
							<option value="0"<?php echo ($params->get('certificate', 0) == 0) ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_NO'); ?></option>
							<option value="1"<?php echo ($params->get('certificate', 0) == 1) ? 'selected="selected"' : '' ?>><?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_YES'); ?></option>
						</select>
						<span class="hint"><?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_EXPLANATION'); ?></span>
					</div>
				</fieldset>
			<?php } else { ?>
				<input type="hidden" name="params[certificate]" value="0" />
			<?php } ?>

			<script type="text/javascript">
				jQuery(document).ready(function(jq){
					var $ = jq;
					if (!$('#badge-published').is(':checked')) {
						$('.badge-field-toggle').hide();
					}

					$('#badge-published').click(function(){
						$('.badge-field-toggle').toggle();
					});
				});
			</script>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_BADGE'); ?></span></legend>
				<?php if (!$this->badge->get('id') || !$this->badge->get('provider_badge_id')) : ?>
					<input type="hidden" name="badge[id]" value="<?php echo $this->badge->get('id'); ?>" />
					<table class="admintable">
						<tbody>
							<tr>
								<th class="key" width="250"><label for="badge-published"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_ENABLED'); ?>:</label></th>
								<td><input type="checkbox" name="badge[published]" id="badge-published" value="1" <?php echo ($this->badge->get('published')) ? 'checked="checked"' : '' ?> /></td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-image"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_IMAGE'); ?>:</label></th>
								<td>
									<?php if ($this->badge->get('img_url')) : ?>
										<?php echo $this->escape(stripslashes($this->badge->get('img_url'))); ?>
										<input type="file" name="badge_image" id="badge-image" />
									<?php else : ?>
										<input type="file" name="badge_image" id="badge-image" />
									<?php endif; ?>
								</td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-provider"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_PROVIDER'); ?>:</label></th>
								<td>
									<select name="badge[provider_name]" id="badge-provider">
										<option value="passport"<?php if ($this->badge->get('provider_name', 'passport') == 'passport') { echo ' selected="selected"'; } ?>>Passport</option>
									</select>
								</td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-criteria"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA'); ?>:</label></th>
								<td>
									<?php
										echo $this->editor('badge[criteria]', $this->escape(stripslashes($this->badge->get('criteria_text'))), 50, 10, 'badge-criteria');
									?>
								</td>
							</tr>
						</tbody>
					</table>
				<?php else : ?>
					<input type="hidden" name="badge[id]" value="<?php echo $this->badge->get('id'); ?>" />
					<table class="admintable">
						<tbody>
							<tr>
								<th class="key" width="250"><label for="badge-published"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_ENABLED'); ?>:</label></th>
								<td><input type="checkbox" name="badge[published]" id="badge-published" value="1" <?php echo ($this->badge->get('published')) ? 'checked="checked"' : '' ?> /></td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-image"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_IMAGE'); ?>:</label></th>
								<td>
									<img src="<?php echo $this->badge->get('img_url'); ?>" width="125" />
								</td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-provider"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_PROVIDER'); ?>:</label></th>
								<td>
									<?php echo $this->escape(stripslashes($this->badge->get('provider_name'))); ?>
								</td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-criteria"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA'); ?>:</label></th>
								<td>
									<?php
										echo $this->editor('badge[criteria]', $this->escape(stripslashes($this->badge->get('criteria_text'))), 35, 5, 'badge-criteria');
									?>
									<a target="_blank" href="<?php echo Request::base(true); ?>/courses/badge/<?php echo $this->badge->get('id'); ?>/criteria"><?php echo Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA'); ?></a>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>
