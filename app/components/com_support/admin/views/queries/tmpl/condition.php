<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// No direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="condition-set">
	<p class="operator">
		<button class="remove" alt="<?php echo Lang::txt('COM_SUPPORT_QUERY_REMOVE'); ?>">&times;</button>
		<?php echo Lang::txt('COM_SUPPORT_QUERY_MATCH',
		'<select id="match' . rand() . '">
			<option value="AND"' . (strtolower($this->condition->operator) == 'and' ? ' selected="selected"' : '' ) . '>' . Lang::txt('COM_SUPPORT_QUERY_ALL') . '</option>
			<option value="OR"' . (strtolower($this->condition->operator) == 'or' ? ' selected="selected"' : '') . '>' . Lang::txt('COM_SUPPORT_QUERY_ANY') . '</option>
		</select>'); ?>
	</p>
	<div class="querycntnr">
		<div class="querystmts querycntnr">
<?php
if ($this->condition->expressions)
{
	foreach ($this->condition->expressions as $expression)
	{
		$operators = $this->conditions->{$expression->fldval}->operators;
		$values    = $this->conditions->{$expression->fldval}->values;
		?>
		<p class="conditions"><button class="remove" alt="<?php echo Lang::txt('COM_SUPPORT_QUERY_REMOVE'); ?>">&times;</button> <select class="fld">
				<option value="open"<?php if ($expression->fldval == 'open') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN'); ?></option>
				<option value="status"<?php if ($expression->fldval == 'status') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS'); ?></option>
				<option value="login"<?php if ($expression->fldval == 'login') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER'); ?></option>
				<option value="owner"<?php if ($expression->fldval == 'owner') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER'); ?></option>
				<option value="group"<?php if ($expression->fldval == 'group') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_GROUP'); ?></option>
				<option value="id"<?php if ($expression->fldval == 'id') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_ID'); ?></option>
				<option value="report"<?php if ($expression->fldval == 'report') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT'); ?></option>
				<?php /*<option value="resolved"<?php if ($expression->fldval == 'resolved') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?></option>*/ ?>
				<option value="severity"<?php if ($expression->fldval == 'severity') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY'); ?></option>
				<option value="tag"<?php if ($expression->fldval == 'tag') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TAG'); ?></option>
				<option value="type"<?php if ($expression->fldval == 'type') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE'); ?></option>
				<option value="created"<?php if ($expression->fldval == 'created') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED'); ?></option>
				<option value="closed"<?php if ($expression->fldval == 'closed') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED'); ?></option>
				<option value="category"<?php if ($expression->fldval == 'category') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY'); ?></option>
			</select>
			<select class="op">
				<?php
				if ($operators)
				{
					foreach ($operators as $operator)
					{
						?>
						<option value="<?php echo $operator->val; ?>"<?php if ($expression->opval == $operator->val) { echo ' selected="selected"'; } ?>><?php echo $operator->label; ?></option>
						<?php
					}
				}
				?>
			</select>
		<?php
		if (is_array($values))
		{
			?>
			<select class="val">
			<?php
			foreach ($values as $value)
			{
				?>
				<option value="<?php echo $value->val; ?>"<?php if ($expression->val == $value->val) { echo ' selected="selected"'; } ?>><?php echo $value->label; ?></option>
				<?php
			}
			?>
			</select>
			<?php
		}
		else
		{
			if ($expression->val == '$me')
			{
				$expression->val = User::get('username');
			}
			?>
			<input type="text" class="val" value="<?php echo $this->escape(stripslashes($expression->val)); ?>" />
			<?php
		}
		?>
		</p>
		<?php
	}
}
?>
			<span class="query-btns">
				<button class="add">+</button>
				<button class="addroot">...</button>
			</span>
		</div>
	</div>
	<?php
	if ($this->condition->nestedexpressions && count($this->condition->nestedexpressions) > 0)
	{
		foreach ($this->condition->nestedexpressions as $nested)
		{
			$this->view('condition')
			     ->set('option', $this->option)
			     ->set('controller', $this->controller)
			     ->set('condition', $nested)
			     ->set('conditions', $this->conditions)
			     ->set('row', $this->row)
			     ->display();
		}
	}
	?>
</fieldset>