<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Kb\Admin\Helpers\Permissions::getActions('article');

$text = ($this->task == 'edit' ? Lang::txt('COM_KB_EDIT') : Lang::txt('COM_KB_NEW'));

Toolbar::title(Lang::txt('COM_KB') . ': ' . Lang::txt('COM_KB_ARTICLE') . ': ' . $text, 'kb');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('article');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_KB_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-section"><?php echo Lang::txt('COM_KB_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo Components\Kb\Admin\Helpers\Html::categories($this->categories, $this->row->get('category'), 'fields[category]', 'field-category'); ?>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_KB_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_KB_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_KB_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KB_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-fulltxt"><?php echo Lang::txt('COM_KB_BODY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('fields[fulltxt]', $this->escape(stripslashes($this->row->get('fulltxt'))), 60, 30, 'field-fulltxt', array('class' => 'required', 'buttons' => array('pagebreak', 'readmore', 'article'))); ?>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_KB_FIELD_TAGS_HINT'); ?>">
					<label for="field-tags"><?php echo Lang::txt('COM_KB_TAGS'); ?>:</label><br />
					<textarea name="tags" id="field-tags" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_KB_FIELD_TAGS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id', 0); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_CREATED'); ?>:</th>
						<td><time datetime="<?php echo $this->row->get('created'); ?>"><?php echo Date::of($this->row->get('created'))->toSql(); ?></time></td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_CREATOR'); ?>:</th>
						<td><?php echo $this->escape($this->row->creator->get('name', Lang::txt('COM_KB_UNKNOWN'))); ?></td>
					</tr>
					<?php if (!$this->row->isNew() && $this->row->get('modified') && $this->row->get('modified') != '0000-00-00 00:00:00') { ?>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?>:</th>
							<td><time datetime="<?php echo $this->row->get('modified'); ?>"><?php echo Date::of($this->row->get('modified'))->toSql(); ?></time></td>
						</tr>
						<?php
						$modifier = User::getInstance($this->row->get('modified_by'));
						if (is_object($modifier)) {?>
							<tr>
								<th class="key"><?php echo Lang::txt('COM_KB_MODIFIER'); ?>:</th>
								<td><?php echo $this->escape($modifier->get('name', Lang::txt('COM_KB_UNKNOWN'))); ?></td>
							</tr>
						<?php } ?>
					<?php } ?>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_HITS'); ?>:</th>
						<td>
							<?php echo $this->row->get('hits', 0); ?>
							<?php if ($this->row->get('hits', 0)) { ?>
								<input type="button" name="reset_hits" id="reset_hits" value="<?php echo Lang::txt('COM_KB_RESET_HITS'); ?>" data-confirm="<?php echo Lang::txt('COM_KB_RESET_HITS_WARNING'); ?>" />
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_KB_VOTES'); ?>:</th>
						<td>
							+<?php echo $this->row->get('helpful', 0); ?> -<?php echo $this->row->get('nothelpful', 0); ?>
							<?php if ($this->row->get('helpful', 0) > 0 || $this->row->get('nothelpful', 0) > 0) { ?>
								<input type="button" name="reset_votes" id="reset_votes" value="<?php echo Lang::txt('COM_KB_RESET_VOTES'); ?>" data-confirm="<?php echo Lang::txt('COM_KB_RESET_VOTES_WARNING'); ?>" />
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_KB_STATE'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_KB_PUBLISH'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-access"><?php echo Lang::txt('COM_KB_ACCESS_LEVEL'); ?>:</label>
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->row->get('access')); ?>
					</select>
				</div>
			</fieldset>

			<fieldset class="adminform paramlist">
				<legend><span><?php echo Lang::txt('COM_KB_PARAMETERS'); ?></span></legend>

				<?php echo $this->params->render(); ?>
			</fieldset>
		</div>
	</div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col span12">
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

	<?php echo Html::input('token'); ?>
</form>
