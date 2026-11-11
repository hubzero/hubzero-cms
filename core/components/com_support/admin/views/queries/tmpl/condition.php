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
        <button class="remove" alt="<?php echo Lang::txt('COM_SUPPORT_QUERY_REMOVE'); ?>">&times;</button>
        <?php
        $randId = rand();
        $andSelected = (strtolower($this->condition->operator) == 'and')
            ? ' selected="selected"'
            : '';
        $orSelected = (strtolower($this->condition->operator) == 'or')
            ? ' selected="selected"'
            : '';
        $selectHtml = '<select id="match' . $randId . '">'
            . '<option value="AND"' . $andSelected . '>' . Lang::txt('COM_SUPPORT_QUERY_ALL') . '</option>'
            . '<option value="OR"' . $orSelected . '>' . Lang::txt('COM_SUPPORT_QUERY_ANY') . '</option>'
            . '</select>';
        echo Lang::txt('COM_SUPPORT_QUERY_MATCH', $selectHtml);
        ?>
    </p>
    <div class="querycntnr">
        <div class="querystmts querycntnr">
<?php
if ($this->condition->expressions) {
    foreach ($this->condition->expressions as $expression) {
        // A stored field name the condition builder no longer knows
        if (!isset($expression->fldval) || !isset($this->conditions->{$expression->fldval})) {
            continue;
        }
        $operators = $this->conditions->{$expression->fldval}->operators;
        $values    = $this->conditions->{$expression->fldval}->values;
        ?>
        <p class="conditions">
            <button class="remove" alt="<?php echo Lang::txt('COM_SUPPORT_QUERY_REMOVE'); ?>">&times;</button>
            <select class="fld">
                <option value="open"<?php echo $expression->fldval == 'open' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN'); ?></option>
                <option value="status"<?php echo $expression->fldval == 'status' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS'); ?></option>
                <option value="login"<?php echo $expression->fldval == 'login' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER'); ?></option>
                <option value="owner"<?php echo $expression->fldval == 'owner' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER'); ?></option>
                <option value="group"<?php echo $expression->fldval == 'group' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_GROUP'); ?></option>
                <option value="id"<?php echo $expression->fldval == 'id' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_ID'); ?></option>
                <option value="report"<?php echo $expression->fldval == 'report' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT'); ?></option>
                <?php /*
                <option value="resolved"<?php if ($expression->fldval == 'resolved') {
                    echo ' selected="selected"';
                                        } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?></option>
                */ ?>
                <option value="severity"<?php echo $expression->fldval == 'severity' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY'); ?></option>
                <option value="tag"<?php echo $expression->fldval == 'tag' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TAG'); ?></option>
                <option value="type"<?php echo $expression->fldval == 'type' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE'); ?></option>
                <option value="created"<?php echo $expression->fldval == 'created' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED'); ?></option>
                <option value="closed"<?php echo $expression->fldval == 'closed' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED'); ?></option>
                <option value="category"<?php echo $expression->fldval == 'category' ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY'); ?></option>
            </select>
            <select class="op">
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
            <select class="val">
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
            <input
                type="text"
                class="val"
                value="<?php echo $this->escape(stripslashes($expression->val)); ?>"
            />
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
</fieldset>
