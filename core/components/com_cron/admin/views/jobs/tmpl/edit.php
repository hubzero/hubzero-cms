<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Cron\Helpers\Permissions::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_CRON') . ': ' . $text, 'cron');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('job');

Html::behavior('calendar');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$this->css('.none {
	display: none;
}
.block {
	display: block;
}');
?>

<?php
foreach ($this->getErrors() as $error) {
    echo '<p class="error">' . $error . '</p>';
}
?>

<?php $formUrl = Route::url('index.php?option=' . $this->option); ?>
<form action="<?php echo $formUrl; ?>" method="post" name="adminForm" id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')); ?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-title">
                        <?php echo Lang::txt('COM_CRON_FIELD_TITLE'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <?php $titleVal = $this->escape(stripslashes($this->row->get('title', ''))); ?>
                    <input type="text" name="fields[title]" id="field-title" class="required"
                        size="30" maxlength="250" value="<?php echo $titleVal; ?>" />
                </div>

                <div class="input-wrap">
                    <label for="field-event">
                        <?php echo Lang::txt('COM_CRON_FIELD_EVENT'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <select name="fields[event]" id="field-event" class="required">
                        <?php $noPlugin = (!$this->row->get('plugin')) ? ' selected="selected"' : ''; ?>
                        <option value=""<?php echo $noPlugin; ?>><?php echo Lang::txt('COM_CRON_SELECT'); ?></option>
                        <?php
                        if ($this->plugins) {
                            foreach ($this->plugins as $plugin) {
                                ?>
                                <?php $pluginLabel = $this->escape(Lang::txt('plg_cron_' . $plugin->plugin)); ?>
                                <optgroup label="<?php echo $pluginLabel; ?>">
                                    <?php
                                    if ($plugin->events) {
                                        foreach ($plugin->events as $event) {
                                            ?>
                                            <?php
                                            $eventVal = $plugin->plugin . '::' . $event['name'];
                                            $isSelected = ($this->row->get('event') == $event['name']);
                                            $eventSel = $isSelected ? ' selected="selected"' : '';
                                            ?>
                            <option value="<?php echo $eventVal; ?>"<?php echo $eventSel; ?>>
                                            <?php echo $this->escape($event['label']); ?>
                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </optgroup>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </fieldset>

            <?php
            if ($this->plugins) {
                foreach ($this->plugins as $plugin) {
                    if (!isset($plugin->events) || !$plugin->events) {
                        continue;
                    }

                    foreach ($plugin->events as $event) {
                        $data = '';
                        $style = 'none';
                        if ($event['name'] == $this->row->get('event')) {
                            $style = 'block';
                            $data = $this->row->get('params');
                        }

                        $out = null;
                        if ($event['params']) {
                            $path = PATH_APP . DS . 'plugins' . DS . 'cron' . DS . $plugin->plugin;
                            if (!is_dir($path)) {
                                $path = PATH_CORE . DS . 'plugins' . DS . 'cron' . DS . $plugin->plugin;
                            }
                            $param = new \Hubzero\Html\Parameter(
                                (is_object($data) ? $data->toString() : $data),
                                $path . DS . $plugin->plugin . '.xml'
                            );
                            $param->addElementPath($path);

                            $html = array();
                            if ($prm = $param->getParams('params', $event['params'])) {
                                foreach ($prm as $p) {
                                    $html[] = '<div class="input-wrap">';
                                    if ($p[0]) {
                                        $html[] = $p[0];
                                        $html[] = $p[1];
                                    } else {
                                        $html[] = $p[1];
                                    }
                                    $html[] = '</div>';
                                }
                            }

                            $out = (!empty($html) ? implode("\n", $html) : $out);
                        }

                        if (!$out) {
                            $out = '<div class="input-wrap"><p><i>'
                                . Lang::txt('COM_CRON_NO_PARAMETERS_FOUND') . '</i></p></div>';
                        }
                        ?>
                        <?php $paramId = 'params-' . $plugin->plugin . '--' . $event['name']; ?>
                        <fieldset class="adminform paramlist eventparams <?php echo $style; ?>"
                            id="<?php echo $paramId; ?>">
                            <legend><span><?php echo Lang::txt('COM_CRON_FIELDSET_PARAMETERS'); ?></span></legend>
                            <?php echo $out; ?>
                        </fieldset>
                        <?php
                    }
                }
            }
            ?>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_CRON_FIELDSET_RECURRENCE'); ?></span></legend>

                <div class="input-wrap">
                    <?php
                    echo Lang::txt('COM_CRON_FIELD_COMMON');
                    $recur = $this->row->get('recurrence');
                    ?>:<br />
                    <select name="fields[recurrence]" id="field-recurrence">
                        <?php $sel = ($recur == '') ? ' selected="selected"' : ''; ?>
                        <option value=""<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_SELECT'); ?>
                        </option>
                        <?php $sel = ($recur == 'custom') ? ' selected="selected"' : ''; ?>
                        <option value="custom"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_CUSTOM'); ?>
                        </option>
                        <?php $sel = ($recur == '0 0 1 1 *') ? ' selected="selected"' : ''; ?>
                        <option value="0 0 1 1 *"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_YEAR'); ?>
                        </option>
                        <?php $sel = ($recur == '0 0 1 * *') ? ' selected="selected"' : ''; ?>
                        <option value="0 0 1 * *"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_MONTH'); ?>
                        </option>
                        <?php $sel = ($recur == '0 0 * * 0') ? ' selected="selected"' : ''; ?>
                        <option value="0 0 * * 0"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_WEEK'); ?>
                        </option>
                        <?php $sel = ($recur == '0 0 * * *') ? ' selected="selected"' : ''; ?>
                        <option value="0 0 * * *"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_DAY'); ?>
                        </option>
                        <?php $sel = ($recur == '0 * * * *') ? ' selected="selected"' : ''; ?>
                        <option value="0 * * * *"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_AN_HOUR'); ?>
                        </option>
                    </select>
                </div>

                <table class="admintable">
                    <tbody id="custom"<?php echo ($this->row->get('recurrence') == 'custom') ? '' : ' class="hide"'; ?>>
                        <tr>
                            <th>
                                <label for="field-minute-c"><?php echo Lang::txt('COM_CRON_FIELD_MINUTE'); ?></label>:
                            </th>
                            <td>
                                <input type="text" name="fields[minute][c]" id="field-minute-c"
                                    value="<?php echo $this->row->get('minute'); ?>" />
                            </td>
                            <td>
                                <?php $min = $this->row->get('minute'); ?>
                                <select name="fields[minute][s]" id="field-minute-s">
                                    <?php $sel = ($min == '') ? ' selected="selected"' : ''; ?>
                                    <option value=""<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?>
                                    </option>
                                    <?php $sel = ($min == '*') ? ' selected="selected"' : ''; ?>
                                    <option value="*"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?>
                                    </option>
                                    <?php $sel = ($min == '*/5') ? ' selected="selected"' : ''; ?>
                                    <option value="*/5"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIVE'); ?>
                                    </option>
                                    <?php $sel = ($min == '*/10') ? ' selected="selected"' : ''; ?>
                                    <option value="*/10"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_TEN'); ?>
                                    </option>
                                    <?php $sel = ($min == '*/15') ? ' selected="selected"' : ''; ?>
                                    <option value="*/15"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIFTEEN'); ?>
                                    </option>
                                    <?php $sel = ($min == '*/30') ? ' selected="selected"' : ''; ?>
                                    <option value="*/30"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_THIRTY'); ?>
                                    </option>
                                    <?php for ($i = 0, $n = 60; $i < $n; $i++) {
                                        $sel = ($min == (string) $i) ? ' selected="selected"' : '';
                                        ?>
                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="field-hour-c"><?php echo Lang::txt('COM_CRON_FIELD_HOUR'); ?></label>:
                            </th>
                            <td>
                                <input type="text" name="fields[hour][c]" id="field-hour-c"
                                    value="<?php echo $this->row->get('hour'); ?>" />
                            </td>
                            <td>
                                <?php $hr = $this->row->get('hour'); ?>
                                <select name="fields[hour][s]" id="field-hour-s">
                                    <?php $sel = ($hr == '') ? ' selected="selected"' : ''; ?>
                                    <option value=""<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?>
                                    </option>
                                    <?php $sel = ($hr == '*') ? ' selected="selected"' : ''; ?>
                                    <option value="*"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?>
                                    </option>
                                    <?php $sel = ($hr == '*/2') ? ' selected="selected"' : ''; ?>
                                    <option value="*/2"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER'); ?>
                                    </option>
                                    <?php $sel = ($hr == '*/4') ? ' selected="selected"' : ''; ?>
                                    <option value="*/4"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_FOUR'); ?>
                                    </option>
                                    <?php $sel = ($hr == '*/6') ? ' selected="selected"' : ''; ?>
                                    <option value="*/6"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX'); ?>
                                    </option>
                                    <?php $sel = ($hr == '0') ? ' selected="selected"' : ''; ?>
                                    <option value="0"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_MIDNIGHT'); ?>
                                    </option>
                                    <?php for ($i = 1, $n = 24; $i < $n; $i++) {
                                        $sel = ($hr == (string) $i) ? ' selected="selected"' : '';
                                        ?>
                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php $dayLabel = Lang::txt('COM_CRON_FIELD_DAY_OF_MONTH'); ?>
                                <label for="field-day-c"><?php echo $dayLabel; ?></label>:
                            </th>
                            <td>
                                <input type="text" name="fields[day][c]" id="field-day-c"
                                    value="<?php echo $this->row->get('day'); ?>" />
                            </td>
                            <td>
                                <?php $dy = $this->row->get('day'); ?>
                                <select name="fields[day][s]" id="field-day-s">
                                    <?php $sel = ($dy == '') ? ' selected="selected"' : ''; ?>
                                    <option value=""<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?>
                                    </option>
                                    <?php $sel = ($dy == '*') ? ' selected="selected"' : ''; ?>
                                    <option value="*"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?>
                                    </option>
                                    <?php for ($i = 1, $n = 32; $i < $n; $i++) {
                                        $sel = ($dy == (string) $i) ? ' selected="selected"' : '';
                                        ?>
                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="field-month-c"><?php echo Lang::txt('COM_CRON_FIELD_MONTH'); ?></label>:
                            </th>
                            <td>
                                <input type="text" name="fields[month][c]" id="field-month-c"
                                    value="<?php echo $this->row->get('month'); ?>" />
                            </td>
                            <td>
                                <?php $mo = $this->row->get('month'); ?>
                                <select name="fields[month][s]" id="field-month-s">
                                    <?php $sel = ($mo == '') ? ' selected="selected"' : ''; ?>
                                    <option value=""<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?>
                                    </option>
                                    <?php $sel = ($mo == '*') ? ' selected="selected"' : ''; ?>
                                    <option value="*"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?>
                                    </option>
                                    <?php $sel = ($mo == '*/2') ? ' selected="selected"' : ''; ?>
                                    <option value="*/2"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER'); ?>
                                    </option>
                                    <?php $sel = ($mo == '*/4') ? ' selected="selected"' : ''; ?>
                                    <option value="*/3"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_THREE'); ?>
                                    </option>
                                    <?php $sel = ($mo == '*/6') ? ' selected="selected"' : ''; ?>
                                    <option value="*/6"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX'); ?>
                                    </option>
                                    <?php $sel = ($mo == '1') ? ' selected="selected"' : ''; ?>
                                    <option value="1"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('JANUARY_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '2') ? ' selected="selected"' : ''; ?>
                                    <option value="2"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('FEBRUARY_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '3') ? ' selected="selected"' : ''; ?>
                                    <option value="3"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('MARCH_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '4') ? ' selected="selected"' : ''; ?>
                                    <option value="4"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('APRIL_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '5') ? ' selected="selected"' : ''; ?>
                                    <option value="5"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('MAY_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '6') ? ' selected="selected"' : ''; ?>
                                    <option value="6"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('JUNE_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '7') ? ' selected="selected"' : ''; ?>
                                    <option value="7"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('JULY_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '8') ? ' selected="selected"' : ''; ?>
                                    <option value="8"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('AUGUST_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '9') ? ' selected="selected"' : ''; ?>
                                    <option value="9"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('SEPTEMBER_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '10') ? ' selected="selected"' : ''; ?>
                                    <option value="10"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('OCTOBER_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '11') ? ' selected="selected"' : ''; ?>
                                    <option value="11"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('NOVEMBER_SHORT'); ?>
                                    </option>
                                    <?php $sel = ($mo == '12') ? ' selected="selected"' : ''; ?>
                                    <option value="12"<?php echo $sel; ?>>
                                        <?php echo Lang::txt('DECEMBER_SHORT'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="field-dayofweek-c">
                                    <?php echo Lang::txt('COM_CRON_FIELD_DAY_OF_WEEK'); ?>
                                </label>:
                            </th>
                            <td>
                                <input type="text" name="fields[dayofweek][c]" id="field-dayofweek-c"
                                    value="<?php echo $this->row->get('dayofweek'); ?>" />
                            </td>
                            <td>
                                <select name="fields[dayofweek][s]" id="field-dayofweek-s">
                                    <option value=""<?php if ($this->row->get('dayofweek') == '') {
                                        echo ' selected="selected"';
                                                    } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
                                    <option value="*"<?php if ($this->row->get('dayofweek') == '*') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?></option>
                                    <option value="0"<?php if ($this->row->get('dayofweek') == '0') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('SUN'); ?></option>
                                    <option value="1"<?php if ($this->row->get('dayofweek') == '1') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('MON'); ?></option>
                                    <option value="2"<?php if ($this->row->get('dayofweek') == '2') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('TUE'); ?></option>
                                    <option value="3"<?php if ($this->row->get('dayofweek') == '3') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('WED'); ?></option>
                                    <option value="4"<?php if ($this->row->get('dayofweek') == '4') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('THU'); ?></option>
                                    <option value="5"<?php if ($this->row->get('dayofweek') == '5') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('FRI'); ?></option>
                                    <option value="6"<?php if ($this->row->get('dayofweek') == '6') {
                                        echo ' selected="selected"';
                                                     } ?>><?php echo Lang::txt('SAT'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_ID'); ?>:</th>
                        <td>
                            <?php $idVal = $this->escape($this->row->get('id')); ?>
                            <?php echo $idVal; ?>
                            <input type="hidden" name="fields[id]" id="field-id"
                                value="<?php echo $idVal; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->creator->get('name'));
                            ?>
                            <?php $createdBy = $this->escape($this->row->get('created_by')); ?>
                            <input type="hidden" name="fields[created_by]" id="field-created_by"
                                value="<?php echo $createdBy; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('created')); ?>
                            <?php
                            $createdLocal = Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s');
                            $createdDate = $this->escape($createdLocal);
                            ?>
                            <input type="hidden" name="fields[created]" id="field-created"
                                value="<?php echo $createdDate; ?>" />
                        </td>
                    </tr>
                <?php if ($this->row->get('modified')) { ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_MODIFIER'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->modifier->get('name', Lang::txt('COM_CRON_UNKNOWN')));
                            ?>
                            <?php $modifiedBy = $this->escape($this->row->get('modified_by')); ?>
                            <input type="hidden" name="fields[modified_by]" id="field-modified_by"
                                value="<?php echo $modifiedBy; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_MODIFIED'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('modified')); ?>
                            <?php
                            $modifiedLocal = Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s');
                            $modifiedDate = $this->escape($modifiedLocal);
                            ?>
                            <input type="hidden" name="fields[modified]" id="field-modified"
                                value="<?php echo $modifiedDate; ?>" />
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($this->row->get('id')) { ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_LAST_RUN'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('last_run')); ?>
                            <?php $lastRun = $this->escape($this->row->get('last_run')); ?>
                            <input type="hidden" name="fields[last_run]" id="field-last_run"
                                value="<?php echo $lastRun; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_NEXT_RUN'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('next_run')); ?>
                            <?php $nextRun = $this->escape($this->row->get('next_run')); ?>
                            <input type="hidden" name="fields[next_run]" id="field-next_run"
                                value="<?php echo $nextRun; ?>" />
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_CRON_FIELD_STATE'); ?>:</label><br />
                    <?php $state = $this->row->get('state'); ?>
                    <select name="fields[state]" id="field-state">
                        <?php $sel = ($state == 0) ? ' selected="selected"' : ''; ?>
                        <option value="0"<?php echo $sel; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
                        <?php $sel = ($state == 1) ? ' selected="selected"' : ''; ?>
                        <option value="1"<?php echo $sel; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
                        <?php $sel = ($state == 2) ? ' selected="selected"' : ''; ?>
                        <option value="2"<?php echo $sel; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <label for="field-publish_up">
                        <?php echo Lang::txt('COM_CRON_FIELD_START_RUNNING'); ?>:
                    </label><br />
                    <?php
                    $pubUp = $this->row->get('publish_up');
                    $pubUpVal = (!$pubUp || $pubUp == '0000-00-00 00:00:00') ? '' : $pubUp;
                    $calAttrs = array('id' => 'field-publish_up');
                    echo Html::input('calendar', 'fields[publish_up]', $this->escape($pubUpVal), $calAttrs);
                    ?>
                </div>

                <div class="input-wrap">
                    <label for="field-publish_down">
                        <?php echo Lang::txt('COM_CRON_FIELD_STOP_RUNNING'); ?>:
                    </label><br />
                    <?php
                    $pubDown = $this->row->get('publish_down');
                    $pubDownVal = (!$pubDown || $pubDown == '0000-00-00 00:00:00') ? '' : $pubDown;
                    $calAttrs = array('id' => 'field-publish_down');
                    echo Html::input('calendar', 'fields[publish_down]', $this->escape($pubDownVal), $calAttrs);
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
