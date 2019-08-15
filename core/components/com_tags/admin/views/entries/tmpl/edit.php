<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Tags\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . $text, 'tags');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('edit');

Html::behavior('framework');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_ADMIN_HINT'); ?>">
<!--				<input type="checkbox" name="fields[admin]" id="field-admin" value="1" <?php if ($this->tag->get('admin') == 1) { echo 'checked="checked"'; } ?> />
-->
				<label for="field-admin"><?php echo Lang::txt('COM_TAGS_FIELD_TYPE'); ?>:</label><br />
				<select name="fields[admin]" id="field-admin">
					<option value="0" <?php if ($this->tag->get('admin') == 0) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FIELD_TYPE_USER'); ?></option>
					<option value="2" <?php if ($this->tag->get('admin') == 2) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FIELD_TYPE_CORE'); ?></option>
					<option value="1" <?php if (is_null($this->tag->get('admin')) || $this->tag->get('admin') == 1) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FIELD_TYPE_ADMIN'); ?></option>
				</select>
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_TAG_HINT'); ?>">
				<label for="field-raw_tag"><?php echo Lang::txt('COM_TAGS_FIELD_RAW_TAG'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[raw_tag]" id="field-raw_tag" class="required" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->tag->get('raw_tag'))); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_TAG_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-tag"><?php echo Lang::txt('COM_TAGS_FIELD_TAG'); ?>:</label><br />
				<input type="text" disabled="disabled" class="disabled" name="fields[tag]" id="field-tag" placeholder="<?php echo Lang::txt('COM_TAGS_FIELD_TAG_PLACEHOLDER'); ?>" maxlength="250" value="<?php echo $this->escape($this->tag->get('tag')); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_ALIAS_HINT'); ?>">
				<label for="field-substitutions"><?php echo Lang::txt('COM_TAGS_FIELD_ALIAS'); ?>:</label><br />
				<textarea name="fields[substitutions]" id="field-substitutions" cols="50" rows="5"><?php echo $this->escape($this->tag->substitutes); ?></textarea>
				<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo Lang::txt('COM_TAGS_FIELD_DESCRIPTION'); ?>:</label><br />
				<?php echo $this->editor('fields[description]', stripslashes($this->tag->get('description')), 50, 4, 'field-description', array('class' => 'minimal', 'buttons' => false)); ?>
			</div>
		</fieldset>
	</div>
	<div class="col span5">
		<table class="meta">
			<tbody>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->tag->get('id'); ?>
						<input type="hidden" name="fields[id]" value="<?php echo $this->tag->get('id'); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						if (!$this->tag->get('created_by') && $this->tag->get('id'))
						{
							if ($logs = $this->tag->logs()->rows())
							{
								foreach ($logs as $log)
								{
									if ($log->get('action') == 'tag_created')
									{
										$this->tag->set('created_by', $log->get('user_id'));
										$this->tag->set('created', $log->get('timestamp'));
										break;
									}
								}
							}
						}
						$name = $this->tag->creator->get('name');
						echo $this->escape(($name ? $name : Lang::txt('COM_TAGS_UNKNOWN')));
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo ($this->tag->created() && $this->tag->created() != '0000-00-00 00:00:00') ? $this->tag->created() : Lang::txt('COM_TAGS_UNKNOWN'); ?>
					</td>
				</tr>
				<?php if ($this->tag->get('id') && $this->tag->wasModified()) { ?>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_MODIFIER'); ?>:</th>
						<td>
							<?php
							if ($this->tag->get('modified_by'))
							{
								$editor = User::getInstance($this->tag->get('modified_by'));
								echo $this->escape(stripslashes($editor->get('name')));
							}
							else
							{
								echo Lang::txt('COM_TAGS_UNKNOWN');
							}
							?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TAGS_FIELD_MODIFIED'); ?>:</th>
						<td>
							<?php echo ($this->tag->modified() && $this->tag->modified() != '0000-00-00 00:00:00') ? $this->tag->modified() : Lang::txt('COM_TAGS_UNKNOWN'); ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<div class="data-wrap">
			<?php
			if (!$this->tag->isNew())
			{
					?>
					<h4><?php echo Lang::txt('COM_TAGS_LOG'); ?></h4>
					<ul class="entry-log">
						<?php
						foreach ($this->tag->logs()->ordered()->rows() as $log)
						{
							$actor = $this->escape(stripslashes($log->actor->get('name')));
							$actor = $actor ?: Lang::txt('JUNKNOWN');

							$s = null;
							$c = '';

							if ($log->get('comments'))
							{
								$data = json_decode($log->get('comments'));
								if (!is_object($data))
								{
									$data = new stdClass;
								}
								if (!isset($data->entries))
								{
									$data->entries = 0;
								}
								switch ($log->get('action'))
								{
									case 'substitute_created':
										$c = 'created';
										$s = Lang::txt('COM_TAGS_LOG_ALIAS_CREATED', (isset($data->raw_tag) ? $data->raw_tag : ''), $log->get('timestamp'), $actor);
									break;

									case 'substitute_edited':
										$c = 'edited';
										$s = Lang::txt('COM_TAGS_LOG_ALIAS_EDITED', (isset($data->raw_tag) ? $data->raw_tag : ''), $log->get('timestamp'), $actor);
									break;

									case 'substitute_deleted':
										$c = 'deleted';
										$s = Lang::txt('COM_TAGS_LOG_ALIAS_DELETED', (isset($data->raw_tag) ? $data->raw_tag : ''), $log->get('timestamp'), $actor);
									break;

									case 'substitute_moved':
										$c = 'moved';
										$s = Lang::txt('COM_TAGS_LOG_ALIAS_MOVED', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
									break;

									case 'tags_removed':
										$c = 'deleted';
										$s = Lang::txt('COM_TAGS_LOG_ASSOC_DELETED', count($data->entries), $data->tbl, $data->objectid, $log->get('timestamp'), $actor);
									break;

									case 'objects_copied':
										$c = 'copied';
										$s = Lang::txt('COM_TAGS_LOG_ASSOC_COPIED', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
									break;

									case 'objects_moved':
										$c = 'moved';
										$s = Lang::txt('COM_TAGS_LOG_ASSOC_MOVED', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
									break;

									case 'objects_removed':
										$c = 'deleted';
										if ($data->objectid || $data->tbl)
										{
											$s = Lang::txt('COM_TAGS_LOG_OBJ_DELETED', count($data->entries), $data->tbl, $data->objectid, $log->get('timestamp'), $actor);
										}
										else
										{
											$s = Lang::txt('COM_TAGS_LOG_OBJ_REMOVED', count($data->entries), $data->tagid, $log->get('timestamp'), $actor);
										}
									break;

									default:
										$c = 'edited';
										$s = Lang::txt('COM_TAGS_LOG_TAG_EDITED', str_replace('_', ' ', $log->get('action')), $log->get('timestamp'), $actor);
									break;
								}
							}
							else
							{
								$c = 'edited';
								$s = Lang::txt('COM_TAGS_LOG_TAG_EDITED', str_replace('_', ' ', $log->get('action')), $log->get('timestamp'), $actor);
							}
							if ($s)
							{
								?>
								<li class="<?php echo $c; ?>">
									<span class="entry-log-data"><?php echo $s; ?></span>
								</li>
								<?php
							}
						}
						?>
					</ul>
					<?php
			}
			?>
		</div>
	</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
