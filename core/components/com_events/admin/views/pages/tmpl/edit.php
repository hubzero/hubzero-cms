<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW'));

Toolbar::title(Lang::txt('COM_EVENTS_PAGE' ) . ': ' . $text, 'event');
Toolbar::save();
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('edit.js');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_EVENTS_PAGE'); ?></span></legend>

				<div class="input-wrap">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . $this->event->id); ?>">
						<?php echo $this->escape(stripslashes($this->event->title)); ?>
					</a>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_EVENTS_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" class="required" value="<?php echo $this->escape(stripslashes($this->page->title)); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_EVENTS_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_EVENTS_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->page->alias)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_EVENTS_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-pagetext"><?php echo Lang::txt('COM_EVENTS_PAGE_TEXT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<?php echo $this->editor('fields[pagetext]', $this->escape(stripslashes($this->page->pagetext)), 40, 20, 'field-pagetext', array('class' => 'required')); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_EVENTS_PAGE_ORDERING'); ?></th>
						<td><?php echo $this->page->ordering; ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_EVENTS_PAGE_CREATED'); ?></th>
						<td><?php echo $this->page->created; ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_EVENTS_PAGE_CREATED_BY'); ?></th>
						<td><?php echo $this->page->created_by; ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_EVENTS_PAGE_LAST_MODIFIED'); ?></th>
						<td><?php echo $this->page->modified; ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_EVENTS_PAGE_LAST_MODIFIED_BY'); ?></th>
						<td><?php echo $this->page->modified_by; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->page->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->page->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
