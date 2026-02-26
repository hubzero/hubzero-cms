<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('calendar.css')
     ->css('jquery.ui.css', 'system')
     ->js();

$browseUrl = Route::url(
    'index.php?option=' . $this->option
);
$saveUrl = Route::url(
    'index.php?option=' . $this->option . '&task=save'
);

$titleVal = $this->escape(
    html_entity_decode(
        stripslashes(
            $this->row->title ? $this->row->title : ''
        )
    )
);
$adresseVal = $this->escape(
    stripslashes(
        $this->row->adresse_info
            ? $this->row->adresse_info : ''
    )
);
$extraVal = $this->escape(
    stripslashes(
        $this->row->extra_info
            ? $this->row->extra_info : ''
    )
);
$emailVal = $this->escape(
    stripslashes(
        $this->row->email ? $this->row->email : ''
    )
);
$restrictedVal = $this->escape(
    stripslashes(
        $this->row->restricted
            ? $this->row->restricted : ''
    )
);

$catLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY');
$reqLabel = Lang::txt('EVENTS_CAL_LANG_REQUIRED');
$titleLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE');
$startDateLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_STARTDATE');
$startTimeLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_STARTTIME');
$endDateLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_ENDDATE');
$endTimeLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_ENDTIME');

if ($this->row->id) {
    $cancelUrl = Route::url(
        'index.php?option=' . $this->option
        . '&task=details&id=' . $this->row->id
    );
} else {
    $cancelUrl = $browseUrl;
}
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <li class="last">
                <a class="icon-browse browse btn"
                   href="<?php echo $browseUrl; ?>">
                    <?php echo Lang::txt('EVENTS_BROWSE'); ?>
                </a>
            </li>
        </ul>
    </div>
</header>

<section class="main section">
    <form action="<?php echo $saveUrl; ?>"
          method="post"
          id="hubForm">
<?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
        <div class="explaination">
            <p><?php echo Lang::txt('EVENTS_CAL_LANG_EXPLANATION'); ?></p>
        </div>
        <fieldset>
            <?php
            $legendTxt = ($this->row->id)
                ? Lang::txt('EVENTS_UPDATE_EVENT')
                : Lang::txt('EVENTS_NEW_EVENT');
            ?>
            <legend><?php echo $legendTxt; ?></legend>

            <label>
                <?php echo $catLabel; ?>:
                <span class="required"><?php echo $reqLabel; ?></span>
                <?php
                echo \Components\Events\Helpers\Html::buildCategorySelect(
                    $this->row->catid,
                    '',
                    $this->gid,
                    $this->option
                );
                ?>
            </label>

            <label>
                <?php echo $titleLabel; ?>:
                <span class="required"><?php echo $reqLabel; ?></span>
                <input type="text"
                       name="title"
                       maxlength="250"
                       value="<?php echo $titleVal; ?>" />
            </label>

            <label>
                <?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>:
                <?php
                echo App::get('editor')->display(
                    'econtent',
                    $this->row->content,
                    '',
                    '',
                    10,
                    15,
                    false,
                    'econtent',
                    null,
                    null,
                    array('class' => 'minimal no-footer')
                );
                ?>
            </label>

            <label>
                <?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>
                <input type="text"
                       name="adresse_info"
                       maxlength="120"
                       value="<?php echo $adresseVal; ?>" />
            </label>

            <label>
                <?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA'); ?>
                <input type="text"
                       name="extra_info"
                       maxlength="240"
                       value="<?php echo $extraVal; ?>" />
            </label>
            <?php
            if ($this->fields) {
                foreach ($this->fields as $field) {
                    $reqSpan = ($field[3])
                        ? '<span class="required">required</span>'
                        : '';
                    ?>
                        <label>
                        <?php echo $field[1]; ?>: <?php echo $reqSpan; ?>
                        <?php
                        if ($field[2] == 'checkbox') {
                            $chk = (stripslashes(end($field)) == 1)
                                ? ' checked="checked"'
                                : '';
                            echo '<input class="option"'
                                . ' type="checkbox"'
                                . ' name="fields[' . $field[0] . ']"'
                                . ' value="1"' . $chk . ' />';
                        } else {
                            $fieldVal = $this->escape(
                                stripslashes(end($field))
                            );
                            echo '<input type="text"'
                                . ' name="fields[' . $field[0] . ']"'
                                . ' size="45" maxlength="255"'
                                . ' value="' . $fieldVal . '" />';
                        }
                        ?>
                        </label>
                    <?php
                }
            }
            ?>
            <label>
                <?php echo Lang::txt('EVENTS_E_TAGS'); ?>
                <?php
                $tf = Event::trigger(
                    'hubzero.onGetMultiEntry',
                    array(array(
                        'tags', 'tags', 'actags',
                        '', $this->lists['tags']
                    ))
                );
                if (count($tf) > 0) {
                    echo $tf[0];
                } else {
                    $tagsVal = $this->escape($this->lists['tags']);
                    echo '<input type="text" name="tags"'
                        . ' value="' . $tagsVal . '"'
                        . ' size="38" />';
                }
                ?>
            </label>
            <fieldset>
                <legend><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_TIME'); ?></legend>
                <label for="publish_up">
                <?php echo $startDateLabel . ' &amp; ' . $startTimeLabel; ?>
                </label>
                <p>
                    <input class="option"
                           type="text"
                           name="publish_up"
                           id="publish_up"
                           size="10"
                           maxlength="10"
                           value="<?php echo $this->times['start_publish']; ?>" />
                    <input class="option"
                           type="text"
                           name="start_time"
                           id="start_time"
                           size="5"
                           maxlength="6"
                           value="<?php echo $this->times['start_time']; ?>" />
                    <?php if ($this->config->getCfg('calUseStdTime') == 'YES') { ?>
                    <input class="option"
                           id="start_pm0"
                           name="start_pm"
                           type="radio"
                           value="0"
                           <?php if (!$this->times['start_pm']) {
                                echo 'checked="checked"';
                           } ?> /><small>AM</small>
                    <input class="option"
                           id="start_pm1"
                           name="start_pm"
                           type="radio"
                           value="1"
                           <?php if ($this->times['start_pm']) {
                                echo 'checked="checked"';
                           } ?> /><small>PM</small>
                    <?php } ?>
                </p>

                <label for="publish_down">
                <?php echo $endDateLabel . ' &amp; ' . $endTimeLabel; ?>
                </label>
                <p>
                    <input class="option"
                           type="text"
                           name="publish_down"
                           id="publish_down"
                           size="10"
                           maxlength="10"
                           value="<?php echo $this->times['stop_publish']; ?>" />
                    <input class="option"
                           type="text"
                           name="end_time"
                           id="end_time"
                           size="5"
                           maxlength="6"
                           value="<?php echo $this->times['end_time']; ?>" />
                    <?php if ($this->config->getCfg('calUseStdTime') == 'YES') { ?>
                    <input class="option"
                           id="end_pm0"
                           name="end_pm"
                           type="radio"
                           value="0"
                           <?php if (!$this->times['end_pm']) {
                                echo 'checked="checked"';
                           } ?> /><small>AM</small>
                    <input class="option"
                           id="end_pm1"
                           name="end_pm"
                           type="radio"
                           value="1"
                           <?php if ($this->times['end_pm']) {
                                echo 'checked="checked"';
                           } ?> /><small>PM</small>
                    <?php } ?>
                </p>

                <label>
                    <?php echo Lang::txt('EVENTS_CAL_TIME_ZONE'); ?>
                    <?php
                    echo \Components\Events\Helpers\Html::buildTimeZoneSelect(
                        $this->times['time_zone'],
                        ''
                    );
                    ?>
                </label>
            </fieldset>
            <input type="hidden"
                   name="state"
                   value="<?php echo $this->escape($this->row->state); ?>" />
        </fieldset><div class="clear"></div>

        <input type="hidden"
               name="email"
               value="<?php echo $emailVal; ?>" />
        <input type="hidden"
               name="restricted"
               value="<?php echo $restrictedVal; ?>" />
        <p class="submit">
            <input class="btn btn-success"
                   type="submit"
                   value="<?php echo Lang::txt('EVENTS_SAVE'); ?>" />

            <a class="btn btn-secondary"
               href="<?php echo $cancelUrl; ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>

        <?php echo Html::input('token'); ?>
        <?php echo Html::input('honeypot'); ?>
        <input type="hidden"
               name="created_by"
               value="<?php echo $this->row->created_by; ?>" />
        <input type="hidden"
               name="option"
               value="<?php echo $this->option; ?>" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden"
               name="id"
               id="event-id"
               value="<?php echo $this->row->id; ?>" />
    </form>
</section>
