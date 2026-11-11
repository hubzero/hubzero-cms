<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Cron\Helpers\Permissions::getActions('component');

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

$view = $this;

// The attribute that marks the current value in the form fields below
$eventSelected = function ($value) use ($view) {
    if ($view->row->get('event') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$recurrenceSelected = function ($value) use ($view) {
    if ($view->row->get('recurrence') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$minuteSelected = function ($value) use ($view) {
    if ($view->row->get('minute') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$hourSelected = function ($value) use ($view) {
    if ($view->row->get('hour') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$daySelected = function ($value) use ($view) {
    if ($view->row->get('day') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$monthSelected = function ($value) use ($view) {
    if ($view->row->get('month') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$dayofweekSelected = function ($value) use ($view) {
    if ($view->row->get('dayofweek') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$stateSelected = function ($value) use ($view) {
    if ($view->row->get('state') == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<?php
foreach ($this->getErrors() as $error) {
    echo '<p class="error">' . $error . '</p>';
}
?>

<form action="<?= Route::url('index.php?option=' . $this->option) ?>" method="post" name="adminForm" id="item-form"
    class="editform form-validate" data-invalid-msg="<?= $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')) ?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-title"><?= Lang::txt('COM_CRON_FIELD_TITLE') ?>: <span
                        class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label><br />
                    <input type="text" name="fields[title]" id="field-title" class="required" size="30" maxlength="250"
                        value="<?= $this->escape(stripslashes($this->row->get('title', ''))) ?>" />
                </div>

                <div class="input-wrap">
                    <label for="field-event"><?= Lang::txt('COM_CRON_FIELD_EVENT') ?>: <span
                        class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label><br />
                    <select name="fields[event]" id="field-event" class="required">
                        <option value=""<?= (!$this->row->get('plugin')) ? ' selected="selected"' : '' ?>>
                            <?= Lang::txt('COM_CRON_SELECT') ?>
                        </option>
                        <?php
                        if ($this->plugins) {
                            foreach ($this->plugins as $plugin) {
                                ?>
                                <optgroup label="<?= $this->escape(Lang::txt('plg_cron_' . $plugin->plugin)) ?>">
                                    <?php
                                    if ($plugin->events) {
                                        foreach ($plugin->events as $event) {
                                            ?>
                                            <?php
                                            $fieldValue = $plugin->plugin . '::' . $event['name'];
                                            $selected = $eventSelected($event['name']);
                                            $optionText = $this->escape($event['label']);
                                            ?>
                                            <option
                                                value="<?= $fieldValue ?>"<?= $selected ?>><?= $optionText ?></option>
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
                                $path . DS . $plugin->plugin . '.xml',
                            );
                            $param->addElementPath($path);

                            $html = [];
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
                            $out = '<div class="input-wrap"><p><i>' . Lang::txt('COM_CRON_NO_PARAMETERS_FOUND')
                                . '</i></p></div>';
                        }
                        ?>
                        <fieldset class="adminform paramlist eventparams <?= $style ?>"
                            id="params-<?= $plugin->plugin . '--' . $event['name'] ?>">
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
                    <?php echo Lang::txt('COM_CRON_FIELD_COMMON'); ?>:<br />
                    <select name="fields[recurrence]" id="field-recurrence">
                        <option value=""<?= $recurrenceSelected('') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_SELECT') ?>
                        </option>
                        <option value="custom"<?= $recurrenceSelected('custom') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_CUSTOM') ?>
                        </option>
                        <option value="0 0 1 1 *"<?= $recurrenceSelected('0 0 1 1 *') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_YEAR') ?>
                        </option>
                        <option value="0 0 1 * *"<?= $recurrenceSelected('0 0 1 * *') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_MONTH') ?>
                        </option>
                        <option value="0 0 * * 0"<?= $recurrenceSelected('0 0 * * 0') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_WEEK') ?>
                        </option>
                        <option value="0 0 * * *"<?= $recurrenceSelected('0 0 * * *') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_DAY') ?>
                        </option>
                        <option value="0 * * * *"<?= $recurrenceSelected('0 * * * *') ?>>
                            <?= Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_AN_HOUR') ?>
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
                                    value="<?= $this->row->get('minute') ?>" />
                            </td>
                            <td>
                                <select name="fields[minute][s]" id="field-minute-s">
                                    <option value=""<?= $minuteSelected('') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') ?>
                                    </option>
                                    <option value="*"<?= $minuteSelected('*') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY') ?>
                                    </option>
                                    <option value="*/5"<?= $minuteSelected('*/5') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIVE') ?>
                                    </option>
                                    <option value="*/10"<?= $minuteSelected('*/10') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_TEN') ?>
                                    </option>
                                    <option value="*/15"<?= $minuteSelected('*/15') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIFTEEN') ?>
                                    </option>
                                    <option value="*/30"<?= $minuteSelected('*/30') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_THIRTY') ?>
                                    </option>
                                    <?php for ($i = 0, $n = 60; $i < $n; $i++) { ?>
                                        <option value="<?= $i ?>"<?= $minuteSelected((string) $i) ?>><?= $i ?></option>
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
                                    value="<?= $this->row->get('hour') ?>" />
                            </td>
                            <td>
                                <select name="fields[hour][s]" id="field-hour-s">
                                    <option value=""<?= $hourSelected('') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') ?>
                                    </option>
                                    <option value="*"<?= $hourSelected('*') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY') ?>
                                    </option>
                                    <option value="*/2"<?= $hourSelected('*/2') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER') ?>
                                    </option>
                                    <option value="*/4"<?= $hourSelected('*/4') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_FOUR') ?>
                                    </option>
                                    <option value="*/6"<?= $hourSelected('*/6') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX') ?>
                                    </option>
                                    <option value="0"<?= $hourSelected("0") ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_MIDNIGHT') ?>
                                    </option>
                                    <?php for ($i = 1, $n = 24; $i < $n; $i++) { ?>
                                        <option value="<?= $i ?>"<?= $hourSelected((string) $i) ?>><?= $i ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="field-day-c"><?= Lang::txt('COM_CRON_FIELD_DAY_OF_MONTH') ?></label>:
                            </th>
                            <td>
                                <input type="text" name="fields[day][c]" id="field-day-c"
                                    value="<?= $this->row->get('day') ?>" />
                            </td>
                            <td>
                                <select name="fields[day][s]" id="field-day-s">
                                    <option value=""<?= $daySelected('') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') ?>
                                    </option>
                                    <option value="*"<?= $daySelected('*') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY') ?>
                                    </option>
                                    <?php for ($i = 1, $n = 32; $i < $n; $i++) { ?>
                                        <option value="<?= $i ?>"<?= $daySelected((string) $i) ?>><?= $i ?></option>
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
                                    value="<?= $this->row->get('month') ?>" />
                            </td>
                            <td>
                                <select name="fields[month][s]" id="field-month-s">
                                    <option value=""<?= $monthSelected('') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') ?>
                                    </option>
                                    <option value="*"<?= $monthSelected('*') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY') ?>
                                    </option>
                                    <option value="*/2"<?= $monthSelected('*/2') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER') ?>
                                    </option>
                                    <option value="*/3"<?= $monthSelected('*/4') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_THREE') ?>
                                    </option>
                                    <option value="*/6"<?= $monthSelected('*/6') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX') ?>
                                    </option>
                                    <option
                                        value="1"<?= $monthSelected('1') ?>><?= Lang::txt('JANUARY_SHORT') ?></option>
                                    <option
                                        value="2"<?= $monthSelected('2') ?>><?= Lang::txt('FEBRUARY_SHORT') ?></option>
                                    <option value="3"<?= $monthSelected('3') ?>><?= Lang::txt('MARCH_SHORT') ?></option>
                                    <option value="4"<?= $monthSelected('4') ?>><?= Lang::txt('APRIL_SHORT') ?></option>
                                    <option value="5"<?= $monthSelected('5') ?>><?= Lang::txt('MAY_SHORT') ?></option>
                                    <option value="6"<?= $monthSelected('6') ?>><?= Lang::txt('JUNE_SHORT') ?></option>
                                    <option value="7"<?= $monthSelected('7') ?>><?= Lang::txt('JULY_SHORT') ?></option>
                                    <option
                                        value="8"<?= $monthSelected('8') ?>><?= Lang::txt('AUGUST_SHORT') ?></option>
                                    <option
                                        value="9"<?= $monthSelected('9') ?>><?= Lang::txt('SEPTEMBER_SHORT') ?></option>
                                    <option
                                        value="10"<?= $monthSelected('10') ?>><?= Lang::txt('OCTOBER_SHORT') ?></option>
                                    <option value="11"<?= $monthSelected('11') ?>>
                                        <?= Lang::txt('NOVEMBER_SHORT') ?>
                                    </option>
                                    <option value="12"<?= $monthSelected('12') ?>>
                                        <?= Lang::txt('DECEMBER_SHORT') ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="field-dayofweek-c"><?= Lang::txt('COM_CRON_FIELD_DAY_OF_WEEK') ?></label>:
                            </th>
                            <td>
                                <input type="text" name="fields[dayofweek][c]" id="field-dayofweek-c"
                                    value="<?= $this->row->get('dayofweek') ?>" />
                            </td>
                            <td>
                                <select name="fields[dayofweek][s]" id="field-dayofweek-s">
                                    <option value=""<?= $dayofweekSelected('') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') ?>
                                    </option>
                                    <option value="*"<?= $dayofweekSelected('*') ?>>
                                        <?= Lang::txt('COM_CRON_FIELD_OPT_EVERY') ?>
                                    </option>
                                    <option value="0"<?= $dayofweekSelected('0') ?>><?= Lang::txt('SUN') ?></option>
                                    <option value="1"<?= $dayofweekSelected('1') ?>><?= Lang::txt('MON') ?></option>
                                    <option value="2"<?= $dayofweekSelected('2') ?>><?= Lang::txt('TUE') ?></option>
                                    <option value="3"<?= $dayofweekSelected('3') ?>><?= Lang::txt('WED') ?></option>
                                    <option value="4"<?= $dayofweekSelected('4') ?>><?= Lang::txt('THU') ?></option>
                                    <option value="5"<?= $dayofweekSelected('5') ?>><?= Lang::txt('FRI') ?></option>
                                    <option value="6"<?= $dayofweekSelected('6') ?>><?= Lang::txt('SAT') ?></option>
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
                            <?php echo $this->escape($this->row->get('id')); ?>
                            <input type="hidden" name="fields[id]" id="field-id"
                                value="<?= $this->escape($this->row->get('id')) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->creator->get('name'));
                            ?>
                            <input type="hidden" name="fields[created_by]" id="field-created_by"
                                value="<?= $this->escape($this->row->get('created_by')) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('created')); ?>
                            <?php
                            $fieldValue = $this->escape(Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s'));
                            ?>
                            <input type="hidden" name="fields[created]" id="field-created" value="<?= $fieldValue ?>" />
                        </td>
                    </tr>
                <?php if ($this->row->get('modified')) { ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_MODIFIER'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->modifier->get('name', Lang::txt('COM_CRON_UNKNOWN')));
                            ?>
                            <input type="hidden" name="fields[modified_by]" id="field-modified_by"
                                value="<?= $this->escape($this->row->get('modified_by')) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_MODIFIED'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('modified')); ?>
                            <?php
                            $fieldValue = $this->escape(Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s'));
                            ?>
                            <input type="hidden" name="fields[modified]" id="field-modified"
                                value="<?= $fieldValue ?>" />
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($this->row->get('id')) { ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_LAST_RUN'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('last_run')); ?>
                            <input type="hidden" name="fields[last_run]" id="field-last_run"
                                value="<?= $this->escape($this->row->get('last_run')) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_CRON_FIELD_NEXT_RUN'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->get('next_run')); ?>
                            <input type="hidden" name="fields[next_run]" id="field-next_run"
                                value="<?= $this->escape($this->row->get('next_run')) ?>" />
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_CRON_FIELD_STATE'); ?>:</label><br />
                    <select name="fields[state]" id="field-state">
                        <option value="0"<?= $stateSelected(0) ?>><?= Lang::txt('JUNPUBLISHED') ?></option>
                        <option value="1"<?= $stateSelected(1) ?>><?= Lang::txt('JPUBLISHED') ?></option>
                        <option value="2"<?= $stateSelected(2) ?>><?= Lang::txt('JTRASHED') ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <label for="field-publish_up"><?= Lang::txt('COM_CRON_FIELD_START_RUNNING') ?>:</label><br />
                    <?php
                    $publishUpText = Html::input(
                        'calendar',
                        'fields[publish_up]',
                        $this->escape(
                            (
                                !$this->row->get('publish_up') || $this->row->get('publish_up') == '0000-00-00 00:00:00'
                                    ? ''
                                    : $this->row->get('publish_up')
                            ),
                        ),
                        ['id' => 'field-publish_up'],
                    );
                    ?>
                    <?= $publishUpText ?>
                </div>

                <div class="input-wrap">
                    <label for="field-publish_down"><?= Lang::txt('COM_CRON_FIELD_STOP_RUNNING') ?>:</label><br />
                    <?php
                    $publishDownText = Html::input(
                        'calendar',
                        'fields[publish_down]',
                        $this->escape(
                            (
                                !$this->row->get('publish_down')
                                    || $this->row->get('publish_down') == '0000-00-00 00:00:00'
                                    ? ''
                                    : $this->row->get('publish_down')
                            ),
                        ),
                        ['id' => 'field-publish_down'],
                    );
                    ?>
                    <?= $publishDownText ?>
                </div>
            </fieldset>

            <?php
                // "detach" lives in the job params Registry (saveTask() rebuilds
                // params from the posted params[...] fields).
                $jobParams = $this->row->get('params');
            if (!is_object($jobParams)) {
                $jobParams = new \Hubzero\Config\Registry($jobParams);
            }
                $detach = (int) $jobParams->get('detach', 0);
            ?>
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_CRON_FIELDSET_EXECUTION'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-detach">
                        <input type="hidden" name="params[detach]" value="0" />
                        <input type="checkbox" name="params[detach]" id="field-detach"
                            value="1"<?= ($detach ? ' checked="checked"' : '') ?> />
                        <?php echo Lang::txt('COM_CRON_FIELD_DETACH'); ?>
                    </label>
                    <p class="hint"><?php echo Lang::txt('COM_CRON_FIELD_DETACH_DESC'); ?></p>
                    <?php if (trim((string) \Config::get('live_site')) === '') { ?>
                        <p class="warning"><?php echo Lang::txt('COM_CRON_DETACH_NO_LIVESITE'); ?></p>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
