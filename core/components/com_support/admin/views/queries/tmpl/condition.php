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
                <option value="open"<?= $expression->fldval == 'open' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') ?>
                </option>
                <option value="status"<?= $expression->fldval == 'status' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') ?>
                </option>
                <option value="login"<?= $expression->fldval == 'login' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') ?>
                </option>
                <option value="owner"<?= $expression->fldval == 'owner' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') ?>
                </option>
                <option value="group"<?= $expression->fldval == 'group' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_GROUP') ?>
                </option>
                <option value="id"<?= $expression->fldval == 'id' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_ID') ?>
                </option>
                <option value="report"<?= $expression->fldval == 'report' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') ?>
                </option>
                <?php /*
                <option value="resolved"<?php if ($expression->fldval == 'resolved') {
                    echo ' selected="selected"';
                                        } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?></option>
                */ ?>
                <option value="severity"<?= $expression->fldval == 'severity' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') ?>
                </option>
                <option value="tag"<?= $expression->fldval == 'tag' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') ?>
                </option>
                <option value="type"<?= $expression->fldval == 'type' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') ?>
                </option>
                <option value="created"<?= $expression->fldval == 'created' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') ?>
                </option>
                <option value="closed"<?= $expression->fldval == 'closed' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') ?>
                </option>
                <option value="category"<?= $expression->fldval == 'category' ? ' selected="selected"' : '' ?>>
                    <?= Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') ?>
                </option>
            </select>
            <select class="op">
                <?php
                if ($operators) {
                    foreach ($operators as $operator) {
                        ?>
                        <option value="<?= $this->escape($operator->val) ?>"<?= $expression->opval == $operator->val ? ' selected="selected"' : '' ?>><?= $this->escape($operator->label) ?></option>
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
                <option value="<?= $this->escape($value->val) ?>"<?= $expression->val == $value->val ? ' selected="selected"' : '' ?>><?= $this->escape($value->label) ?></option>
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
