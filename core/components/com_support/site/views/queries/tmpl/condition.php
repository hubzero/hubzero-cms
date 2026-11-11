<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="condition-set">
    <p class="operator">
        <button class="remove" alt="Remove">&times;</button>
        <?php echo Lang::txt(
            'COM_SUPPORT_QUERY_MATCH',
            '<select aria-label="' . Lang::txt('COM_SUPPORT_QUERY_MATCH_OPERATOR') . '">
			<option value="AND"' . (strtolower($this->condition->operator) == 'and' ? ' selected="selected"' : '') . '>' . Lang::txt('COM_SUPPORT_QUERY_ALL') . '</option>
			<option value="OR"' . (strtolower($this->condition->operator) == 'or' ? ' selected="selected"' : '') . '>' . Lang::txt('COM_SUPPORT_QUERY_ANY') . '</option>
		</select>',
        ); ?>
    </p>
    <div>
        <div class="querystmts">
<?php
if ($this->condition->expressions) {
    foreach ($this->condition->expressions as $expression) {
        $operators = $this->conditions->{$expression->fldval}->operators;
        $values    = $this->conditions->{$expression->fldval}->values;
        ?>
        <p class="conditions"><button class="remove" alt="Remove">&times;</button> <select class="fld" aria-label="<?php echo Lang::txt('COM_SUPPORT_QUERY_FIELD'); ?>">
            <option value="open"<?php echo $expression->fldval == 'open' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN'); ?></option>
                <option value="status"<?php echo $expression->fldval == 'status' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS'); ?></option>
                <option value="login"<?php echo $expression->fldval == 'login' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER'); ?></option>
                <option value="owner"<?php echo $expression->fldval == 'owner' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER'); ?></option>
                <option value="group"<?php echo $expression->fldval == 'group' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_GROUP'); ?></option>
                <option value="id"<?php echo $expression->fldval == 'id' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_ID'); ?></option>
                <option value="report"<?php echo $expression->fldval == 'report' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT'); ?></option>
                <?php /*<option value="resolved"<?php if ($expression->fldval == 'resolved') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?></option>*/ ?>
                <option value="severity"<?php echo $expression->fldval == 'severity' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY'); ?></option>
                <option value="tag"<?php echo $expression->fldval == 'tag' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TAG'); ?></option>
                <option value="type"<?php echo $expression->fldval == 'type' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE'); ?></option>
                <option value="created"<?php echo $expression->fldval == 'created' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED'); ?></option>
                <option value="closed"<?php echo $expression->fldval == 'closed' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED'); ?></option>
                <option value="category"<?php echo $expression->fldval == 'category' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY'); ?></option>
            </select>
            <select class="op" aria-label="<?php echo Lang::txt('COM_SUPPORT_QUERY_OPERATOR'); ?>">
        <?php
        if ($operators) {
            foreach ($operators as $operator) {
                ?>
                <option value="<?php echo $this->escape($operator->val); ?>"<?php echo $expression->opval == $operator->val ? ' selected="selected"' : ''; ?>><?php echo $this->escape($operator->label); ?></option>
                <?php
            }
        }
        ?>
            </select>
        <?php
        if (is_array($values)) {
            ?>
            <select class="val" aria-label="<?php echo Lang::txt('COM_SUPPORT_QUERY_VALUE'); ?>">
            <?php
            foreach ($values as $value) {
                ?>
                <option value="<?php echo $this->escape($value->val); ?>"<?php echo $expression->val == $value->val ? ' selected="selected"' : ''; ?>><?php echo $this->escape($value->label); ?></option>
                <?php
            }
            ?>
            </select>
            <?php
        } else {
            if ($expression->val == '$me') {
                $expression->val = User::get('username');
            }
            ?>
            <input type="text" class="val" aria-label="<?php echo Lang::txt('COM_SUPPORT_QUERY_VALUE'); ?>" value="<?php echo $this->escape(stripslashes($expression->val)); ?>" />
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
if ($this->condition->nestedexpressions && count($this->condition->nestedexpressions) > 0) {
    foreach ($this->condition->nestedexpressions as $nested) {
        $this
             ->view('condition')
             ->set('option', $this->option)
             ->set('controller', $this->controller)
             ->set('condition', $nested)
             ->set('conditions', $this->conditions)
             ->set('row', $this->row)
             ->display();
    }
}
?>
    </div>
</fieldset>