<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->ajax)
{
	$this->css('jquery.datepicker.css', 'system')
		 ->css('jquery.timepicker.css', 'system')
		 ->css()
		 ->js('jquery.timepicker', 'system')
		 ->js();
}

$color = $this->row->get('color');
$lists = $this->todo->getLists($this->model->get('id'));

$used = array();
if (!empty($lists))
{
	foreach ($lists as $list)
	{
		$used[] = $list->color;
	}
}

if (!$this->row->get('id') && $this->list && in_array(trim($this->list), $used ))
{
	$color = trim($this->list);
}
$class = $color ? 'pin_' . $color : 'pin_grey';

?>

<div id="abox-content">
<h3><?php echo $this->row->get('id') ? Lang::txt('PLG_PROJECTS_TODO_EDIT_TODO') : Lang::txt('PLG_PROJECTS_TODO_ADD_TODO'); ?></h3>

<div class="pinboard">
	<form action="<?php echo Route::url('index.php?option='.$this->option . '&alias=' . $this->model->get('alias') . '&active=todo'); ?>" method="post" id="plg-form" >
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" id="pid" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
			<input type="hidden" name="active" value="todo" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="page" id="tdpage" value="item" />
			<input type="hidden" name="todoid" id="todoid" value="<?php echo $this->row->get('id'); ?>" />
		</fieldset>
		<section class="section intropage">
			<div id="td-item" class="<?php echo $class; ?>">
				<span class="pin">&nbsp;</span>
				<div class="todo-content">
					<textarea name="content" rows="10" cols="25" placeholder="<?php echo Lang::txt('PLG_PROJECTS_TODO_TYPE_TODO'); ?>"><?php echo $this->row->get('details') ? stripslashes($this->row->get('details')) :  stripslashes($this->row->get('content')); ?></textarea>
					<div class="todo-edits">
						<?php if (count($lists) > 0) { ?>
						<label><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_TODO_CHOOSE_LIST')); ?>:
							<select name="list">
								<option value="none" <?php if ($color == '') { echo 'selected="selected"'; } ?>><?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_TO_NO_LIST'); ?></option>
							<?php foreach ($lists as $list) { ?>
								<option value="<?php echo $list->color; ?>" <?php if ($list->color == $color) { echo 'selected="selected"'; } ?>><?php echo stripslashes($list->todolist); ?></option>
							<?php } ?>
							</select>
						</label>
						<?php } ?>
						<label id="td-selector"><?php echo Lang::txt('PLG_PROJECTS_TODO_TODO_ASSIGNED_TO'); ?>
							<select name="assigned">
								<option value=""><?php echo Lang::txt('PLG_PROJECTS_TODO_NOONE'); ?></option>
							<?php foreach ($this->team as $member) { ?>
								<?php if ($member->userid && $member->userid != 0) {
									$team_ids[] = $member->userid; ?>
								<option value="<?php echo $member->userid; ?>" class="nameopt" <?php if ($member->userid == $this->row->get('assigned_to')) { echo 'selected="selected"'; } ?>><?php echo $member->name; ?></option>
								<?php } ?>
							<?php } ?>
							</select>
						</label>
						<label><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_DUE')); ?>
							<input type="text" name="due" id="dued" class="duebox" placeholder="mm/dd/yyyy" value="<?php echo $this->row->due() ? Date::of(strtotime($this->row->due('date')))->toLocal('m/d/Y') : ''; ?>" />
						</label>
						<p class="submitarea">
							<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_TODO_SAVE'); ?>" class="btn" />
						</p>
					</div>
				</div>
			</div>
		</section>
		<?php echo Html::input('token'); ?>
	</form>
</div>
</div>