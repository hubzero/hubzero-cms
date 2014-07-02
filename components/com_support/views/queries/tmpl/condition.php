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
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="condition-set">
	<p class="operator">
		<button class="remove" alt="Remove">&times;</button>
		Match
		<select>
			<option value="AND"<?php if (strtolower($this->condition->operator) == 'and') { echo ' selected="selected"'; } ?>>all</option>
			<option value="OR"<?php if (strtolower($this->condition->operator) == 'or') { echo ' selected="selected"'; } ?>>any</option>
		</select>
		of the following:
	</p>
	<div>
		<div class="querystmts">
<?php
if ($this->condition->expressions)
{
	foreach ($this->condition->expressions as $expression)
	{
		$operators = $this->conditions->{$expression->fldval}->operators;
		$values    = $this->conditions->{$expression->fldval}->values;
?>
		<p class="conditions"><button class="remove" alt="Remove">&times;</button> <select class="fld">
				<option value="open"<?php if ($expression->fldval == 'open') { echo ' selected="selected"'; } ?>>Open/Closed</option>
				<option value="status"<?php if ($expression->fldval == 'status') { echo ' selected="selected"'; } ?>>Status</option>
				<option value="login"<?php if ($expression->fldval == 'login') { echo ' selected="selected"'; } ?>>Submitter</option>
				<option value="owner"<?php if ($expression->fldval == 'owner') { echo ' selected="selected"'; } ?>>Owner</option>
				<option value="group"<?php if ($expression->fldval == 'group') { echo ' selected="selected"'; } ?>>Group</option>
				<option value="id"<?php if ($expression->fldval == 'id') { echo ' selected="selected"'; } ?>>ID</option>
				<option value="report"<?php if ($expression->fldval == 'report') { echo ' selected="selected"'; } ?>>Report</option>
				<option value="resolved"<?php if ($expression->fldval == 'resolved') { echo ' selected="selected"'; } ?>>Resolution</option>
				<option value="severity"<?php if ($expression->fldval == 'severity') { echo ' selected="selected"'; } ?>>Severity</option>
				<option value="tag"<?php if ($expression->fldval == 'tag') { echo ' selected="selected"'; } ?>>Tag</option>
				<option value="type"<?php if ($expression->fldval == 'type') { echo ' selected="selected"'; } ?>>Type</option>
				<option value="created"<?php if ($expression->fldval == 'created') { echo ' selected="selected"'; } ?>>Created</option>
				<option value="category"<?php if ($expression->fldval == 'category') { echo ' selected="selected"'; } ?>>Category</option>
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
				$juser = JFactory::getUser();
				$expression->val = $juser->get('username');
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
			<span>
				<button class="add">+</button>
				<button class="addroot">...</button>
			</span>
		</div>
<?php
	if ($this->condition->nestedexpressions && count($this->condition->nestedexpressions) > 0)
	{
		foreach ($this->condition->nestedexpressions as $nested)
		{
			$view = new JView(array(
				'name'   => $this->controller,
				'layout' => 'condition'
			));
			$view->option     = $this->option;
			$view->controller = $this->controller;
			$view->condition  = $nested;
			$view->conditions = $this->conditions;
			$view->row        = $this->row;
			$view->display();
		}
	}
?>
	</div>
</fieldset>