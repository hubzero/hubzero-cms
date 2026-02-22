<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit') ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW');

Toolbar::title(Lang::txt('COM_EVENTS_EVENT') . ': ' . $text, 'event.png');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('event');

$xprofilec = User::getInstance($this->row->created_by);
$xprofilem = User::getInstance($this->row->modified_by);
$userm = is_object($xprofilem) ? $xprofilem->get('name') : '';
$userc = is_object($xprofilec) ? $xprofilec->get('name') : '';

$params = new \Hubzero\Html\Parameter(
    $this->row->params,
    Component::path($this->option) . DS . 'events.xml'
);

$this->js('events.js');

$formAction = Route::url('index.php?option=' . $this->option);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>

<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    class="editform form-validate"
    id="item-form"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_EVENTS_EVENT'); ?></span></legend>

                <div class="input-wrap">
                    <?php
                    $titleLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TITLE');
                    $reqLabel = Lang::txt('JOPTION_REQUIRED');
                    $titleValue = $this->escape(html_entity_decode(stripslashes($this->row->title)));
                    ?>
                    <label for="field-title">
                        <?php echo $titleLabel; ?>: <span class="required"><?php echo $reqLabel; ?></span>
                    </label><br />
                    <input type="text"
                        name="title"
                        id="field-title"
                        class="required"
                        maxlength="250"
                        value="<?php echo $titleValue; ?>"
                    /></td>
                </div>

                <div class="input-wrap">
                    <?php
                    $catLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY');
                    ?>
                    <label for="catid">
                        <?php echo $catLabel; ?>:<span class="required"><?php echo $reqLabel; ?></span>
                    </label><br />
                    <?php
                    echo \Components\Events\Helpers\Html::buildCategorySelect(
                        $this->row->catid,
                        '',
                        0,
                        $this->option
                    );
                    ?></td>
                </div>

                <div class="input-wrap">
                    <?php $actLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACTIVITY'); ?>
                    <label for="field-econtent"><?php echo $actLabel; ?>:</label><br />
                    <?php
                    echo $this->editor(
                        'econtent',
                        $this->row->content,
                        '45',
                        '10',
                        'field-econtent'
                    );
                    ?></td>
                </div>

                <div class="input-wrap">
                    <?php
                    $addrLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ADRESSE');
                    $addrValue = $this->escape(stripslashes($this->row->adresse_info));
                    ?>
                    <label for="field-adresse_info"><?php echo $addrLabel; ?>:</label><br />
                    <input type="text"
                        name="adresse_info"
                        id="field-adresse_info"
                        maxlength="120"
                        value="<?php echo $addrValue; ?>"
                    /></td>
                </div>

                <div class="input-wrap">
                    <?php
                    $contactLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CONTACT');
                    $contactValue = $this->escape(stripslashes($this->row->contact_info));
                    ?>
                    <label for="field-contact_info"><?php echo $contactLabel; ?>:</label><br />
                    <input type="text"
                        name="contact_info"
                        id="field-contact_info"
                        maxlength="120"
                        value="<?php echo $contactValue; ?>"
                    /></td>
                </div>

                <div class="input-wrap">
                    <?php
                    $extraLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_EXTRA');
                    $extraValue = $this->escape(stripslashes($this->row->extra_info));
                    ?>
                    <label for="field-extra_info"><?php echo $extraLabel; ?>:</label><br />
                    <input type="text"
                        name="extra_info"
                        id="field-extra_info"
                        maxlength="240"
                        value="<?php echo $extraValue; ?>"
                    /></td>
                </div>
                    <?php
                    foreach ($this->fields as $field) {
                        $reqSpan = ($field[3])
                            ? '<span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>'
                            : '';
                        ?>
                        <div class="input-wrap">
                            <label for="field-<?php echo $field[0]; ?>">
                                <?php echo $field[1]; ?>: <?php echo $reqSpan; ?>
                            </label><br />
                            <?php
                            if ($field[2] == 'checkbox') {
                                echo '<input type="checkbox"'
                                    . ' name="fields[' . $field[0] . ']"'
                                    . ' id="field-' . $field[0] . '"'
                                    . ' value="1" class="required"';
                                if (stripslashes(end($field)) == 1) {
                                    echo ' checked="checked"';
                                }
                                echo ' />';
                            } else {
                                echo '<input type="text"'
                                    . ' name="fields[' . $field[0] . ']"'
                                    . ' id="field-' . $field[0] . '"'
                                    . ' class="required" maxlength="255"'
                                    . ' value="' . $this->escape(end($field)) . '" />';
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                <div class="input-wrap">
                    <?php
                    $tagsLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TAGS');
                    $tagsValue = (isset($this->tags)) ? $this->escape($this->tags) : '';
                    ?>
                    <label for="field-tags"><?php echo $tagsLabel; ?>:</label><br />
                    <input type="text"
                        name="tags"
                        id="field-tags"
                        value="<?php echo $tagsValue; ?>"
                    />
                </div>
            </fieldset>
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_EVENTS_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <?php $startLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STARTDATE'); ?>
                    <label for="field-publish_up"><?php echo $startLabel; ?></label><br />
                    <?php
                    echo Html::input(
                        'calendar',
                        'publish_up',
                        $this->row->publish_up,
                        array('id' => 'field-publish_up')
                    );
                    ?>
                </div>

                <div class="input-wrap">
                    <?php $endLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ENDDATE'); ?>
                    <label for="field-publish_down"><?php echo $endLabel; ?></label><br />
                    <?php
                    echo Html::input(
                        'calendar',
                        'publish_down',
                        $this->row->publish_down,
                        array('id' => 'field-publish_down')
                    );
                    ?>
                </div>

                <div class="input-wrap">
                    <label for="time_zone"><?php echo Lang::txt('COM_EVENTS_CAL_TIME_ZONE'); ?></label>
                    <?php
                    echo \Components\Events\Helpers\Html::buildTimeZoneSelect(
                        $this->row->time_zone,
                        ''
                    );
                    ?>
                </div>
            </fieldset>

            <?php if ($this->row->scope == 'group') : ?>
                <fieldset class="adminform">
                    <?php $recurLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE'); ?>
                    <legend><span><?php echo $recurLabel; ?></span></legend>

                    <div class="input-wrap">
                        <label for="field-repeating_rule"><?php echo $recurLabel; ?></label><br />
                        <input type="text"
                            name="repeating_rule"
                            value="<?php echo stripslashes($this->row->repeating_rule); ?>"
                        />
                        <?php
                        $recurHint = Lang::txt(
                            'COM_EVENTS_CAL_LANG_EVENT_RECURRENCE_HINT',
                            'http://www.kanzaki.com/docs/ical/rrule.html'
                        );
                        ?>
                        <span class="block-hint"><?php echo $recurHint; ?></span>
                    </div>
                </fieldset>
            <?php endif; ?>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_EVENTS_REGISTRATION'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-registerby"><?php echo Lang::txt('COM_EVENTS_REGISTER_BY'); ?>:</label><br />
                    <?php
                    echo Html::input(
                        'calendar',
                        'registerby',
                        $this->row->registerby,
                        array('id' => 'field-registerby')
                    );
                    ?>
                </div>

                <?php $emailHint = Lang::txt('COM_EVENTS_EMAIL_HINT'); ?>
                <div class="input-wrap" data-hint="<?php echo $emailHint; ?>">
                    <label for="field-email"><?php echo Lang::txt('COM_EVENTS_EMAIL'); ?>:</label><br />
                    <input type="text"
                        name="email"
                        id="field-email"
                        value="<?php echo $this->escape($this->row->email); ?>"
                    />
                    <span class="hint"><?php echo $emailHint; ?></span>
                </div>

                <?php $restrictedHint = Lang::txt('COM_EVENTS_RESTRICTED_HINT'); ?>
                <div class="input-wrap" data-hint="<?php echo $restrictedHint; ?>">
                    <label for="field-restricted"><?php echo Lang::txt('COM_EVENTS_RESTRICTED'); ?>:</label><br />
                    <input type="text"
                        name="restricted"
                        id="field-restricted"
                        value="<?php echo $this->escape($this->row->restricted); ?>"
                    />
                    <span class="hint"><?php echo $restrictedHint; ?></span>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
                        <td>
                            <?php
                            if ($this->row->state > 0) {
                                echo Lang::txt('COM_EVENTS_EVENT_PUBLISHED');
                            } elseif ($this->row->state < 0) {
                                echo Lang::txt('COM_EVENTS_EVENT_ARCHIVED');
                            } else {
                                echo Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED');
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $createdLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED'); ?>
                        <th scope="row"><?php echo $createdLabel; ?></th>
                        <td>
                        <?php if ($this->row->created) { ?>
                            <?php echo Date::of($this->row->created)->toLocal('F d, Y @ g:ia'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED_BY'); ?></th>
                        <td><?php echo $userc; ?>
                        <?php } else { ?>
                            <?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_NEWEVENT'); ?>
                        <?php } ?>
                        </td>
                    </tr>
                <?php if ($this->row->modified && $this->row->modified != '0000-00-00 00:00:00') { ?>
                    <tr>
                        <?php $modLabel = Lang::txt('COM_EVENTS_CAL_LANG_EVENT_MODIFIED'); ?>
                        <th scope="row"><?php echo $modLabel; ?></th>
                        <td>
                        <?php if ($this->row->modified) { ?>
                            <?php echo Date::of($this->row->modified)->toLocal('F d, Y @ g:ia'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_MODIFIED_BY'); ?></th>
                        <td><?php echo $userm; ?>
                        <?php } else { ?>
                            <?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_NOTMODIFIED'); ?>
                        <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <fieldset class="adminform paramlist">
                <legend><span><?php echo Lang::txt('COM_EVENTS_REGISTRATION_FIELDS'); ?></span></legend>
                <?php echo $params->render(); ?>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="images" value="" />

    <?php echo Html::input('token'); ?>
</form>
