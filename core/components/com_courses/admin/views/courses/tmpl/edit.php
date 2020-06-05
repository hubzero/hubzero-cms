<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES').': ' . $text, 'courses');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('course');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('jquery.fileuploader.js', 'system');
$this->js();
$this->css();
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
				<input type="hidden" name="task" value="save" />

				<div class="input-wrap">
					<label for="field-group_id"><?php echo Lang::txt('COM_COURSES_FIELD_GROUP'); ?>:</label><br />
					<select name="fields[group_id]" id="field-group_id">
						<option value="0"<?php if (!$this->row->get('group_id')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_NONE'); ?></option>
						<?php
						$filters = array(
							'authorized' => 'admin',
							'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
							'type'       => array(1, 3),
							'sortby'     => 'description'
						);
						$groups = \Hubzero\User\Group::find($filters);
						if ($groups)
						{
							foreach ($groups as $group)
							{
								?>
								<option value="<?php echo $group->gidNumber; ?>"<?php if ($group->gidNumber == $this->row->get('group_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($group->description); ?> (<?php echo $this->escape($group->cn); ?>)</option>
								<?php
							}
						}
						?>
					</select>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->row->get('alias')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" value="<?php echo $this->escape($this->row->get('title')); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_BLURB_HINT'); ?>">
					<label for="field-blurb"><?php echo Lang::txt('COM_COURSES_FIELD_BLURB'); ?>:</label><br />
					<textarea name="fields[blurb]" id="field-blurb" cols="40" rows="3"><?php echo $this->escape($this->row->get('blurb')); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_BLURB_HINT'); ?></span>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_LENGTH_HINT'); ?>">
							<label for="field-length"><?php echo Lang::txt('COM_COURSES_FIELD_LENGTH'); ?>:</label><br />
							<input type="text" name="fields[length]" id="field-length" value="<?php echo $this->escape($this->row->get('length')); ?>" />
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_LENGTH_HINT'); ?></span>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_EFFORT_HINT'); ?>">
							<label for="field-effort"><?php echo Lang::txt('COM_COURSES_FIELD_EFFORT'); ?>:</label><br />
							<input type="text" name="fields[effort]" id="field-effort" value="<?php echo $this->escape($this->row->get('effort')); ?>" />
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_EFFORT_HINT'); ?></span>
						</div>
					</div>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_DESCRIPTION_HINT'); ?>">
					<label for="field-description"><?php echo Lang::txt('COM_COURSES_FIELD_DESCRIPTION'); ?>:</label><br />
					<?php echo $this->editor('fields[description]', $this->escape($this->row->description('raw')), 40, 15, 'field-description'); ?>
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_DESCRIPTION_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_TAGS_HINT'); ?>">
					<label for="field-tags"><?php echo Lang::txt('COM_COURSES_FIELD_TAGS'); ?>:</label><br />
					<textarea name="tags" id="field-tags" cols="40" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_MANAGERS'); ?></span></legend>
				<?php if ($this->row->get('id')) { ?>
					<iframe height="400" name="managers" id="managers" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=managers&tmpl=component&id=' . $this->row->get('id')); ?>"></iframe>
				<?php } else { ?>
					<p class="warning"><?php echo Lang::txt('COM_COURSES_FIELDSET_MANAGERS_WARNING'); ?></p>
				<?php } ?>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>
					</tr>
					<?php if ($this->row->get('created')) { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_CREATED'); ?></th>
							<td><time datetime="<?php echo $this->escape($this->row->get('created')); ?>"><?php echo $this->escape(Date::of($this->row->get('created'))->toLocal()); ?></time></td>
						</tr>
					<?php } ?>
					<?php if ($this->row->get('created_by')) { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_CREATOR'); ?></th>
							<td><?php
							$creator = User::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($creator->get('name'))); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
						<option value="3"<?php if ($this->row->get('state') == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
					</select>
				</div>
			</fieldset>

			<?php
			if ($plugins = Event::trigger('courses.onCourseEdit'))
			{
				$data = $this->row->get('params');

				foreach ($plugins as $plugin)
				{
					$param = new \Hubzero\Html\Parameter(
						(is_object($data) ? $data->toString() : $data),
						PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
					);
					$out = $param->render('params', 'onCourseEdit');
					if (!$out)
					{
						continue;
					}
					?>
					<fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
						<legend><?php echo Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']); ?></legend>
						<?php echo $out; ?>
					</fieldset>
					<?php
				}
			}
			?>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_IMAGE'); ?></span></legend>

				<?php
				if ($this->row->exists()) {
					$logo = stripslashes($this->row->get('logo'));
					$pics = explode(DS, $logo);
					$file = end($pics);
				?>
				<div class="uploader-wrap">
					<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=logo&task=upload&type=course&id=' . $this->row->get('id') . '&no_html=1&' . Session::getFormToken() . '=1'); ?>" data-instructions="<?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?>">
						<noscript>
							<iframe height="350" name="filer" id="filer" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=logo&tmpl=component&file=' . $file . '&type=course&id=' . $this->row->get('id')); ?>"></iframe>
						</noscript>
					</div>
				</div>
					<?php
					$width = 0;
					$height = 0;
					$fsize = 0;

					$pic  = 'blank.png';
					$path = '/core/components/com_courses/admin/assets/img';

					if ($logo)
					{
						$pathl = substr(PATH_APP, strlen(PATH_ROOT)) . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->row->get('id');

						if (file_exists(PATH_ROOT . $pathl . DS . $logo))
						{
							$this_size = filesize(PATH_ROOT . $pathl . DS . $file);
							list($width, $height, $type, $attr) = getimagesize(PATH_ROOT . $pathl . DS . $file);
							$pic  = $file;
							$path = $pathl;
						}
						else
						{
							$logo = null;
						}
					}
					?>
					<div id="img-container">
						<img id="img-display" src="<?php echo rtrim(Request::root(true), '/') . $path . '/' . $pic; ?>" alt="<?php echo Lang::txt('COM_COURSES_LOGO'); ?>" />
						<input type="hidden" name="currentfile" id="currentfile" value="<?php echo $this->escape($logo); ?>" />
					</div>
					<table class="formed">
						<tbody>
							<tr>
								<th><?php echo Lang::txt('COM_COURSES_FILE'); ?>:</th>
								<td>
									<span id="img-name"><?php echo $this->row->get('logo', Lang::txt('COM_COURSES_NONE')); ?></span>
								</td>
								<td>
									<a id="img-delete" <?php echo $logo ? '' : 'class="hide"'; ?> href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=logo&tmpl=component&task=remove&currentfile=' . $logo . '&type=course&id=' . $this->row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>" data-defaultimg="<?php echo rtrim(Request::root(true), '/'); ?>/core/components/com_courses/admin/assets/img/blank.png">[ x ]</a>
								</td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_COURSES_PICTURE_SIZE'); ?>:</th>
								<td><span id="img-size"><?php echo \Hubzero\Utility\Number::formatBytes($fsize); ?></span></td>
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
				<?php
				} else {
					echo '<p class="warning">'.Lang::txt('COM_COURSES_PICTURE_ADDED_LATER').'</p>';
				}
				?>
			</fieldset>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>
