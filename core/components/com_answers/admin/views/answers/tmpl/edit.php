<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Answers\Helpers\Permissions::getActions('answer');

$text = ($this->task == 'edit'
    ? Lang::txt('JACTION_EDIT')
    : Lang::txt('JACTION_CREATE'));

Toolbar::title(
    Lang::txt('COM_ANSWERS_TITLE') . ': '
        . Lang::txt('COM_ANSWERS_QUESTIONS') . ': ' . $text,
    'answers'
);
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('response');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$formAction = Route::url(
    'index.php?option=' . $this->option
        . '&controller=' . $this->controller
);
$invalidMsg = $this->escape(
    Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')
);
$anonChecked = ($this->row->get('anonymous'))
    ? 'checked="checked"'
    : '';
$questionVal = $this->escape(
    strip_tags($this->question->get('subject'))
);
$stateChecked = $this->row->get('state')
    ? 'checked="checked"'
    : '';
$stateLabel = ($this->row->get('state') == 1)
    ? Lang::txt('COM_ANSWERS_STATE_ACCEPTED')
    : Lang::txt('COM_ANSWERS_STATE_UNACCEPTED');
$createdByVal = $this->escape(
    $this->row->get('created_by', User::get('id'))
);
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    class="form-validate"
    id="item-form"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <input
                        type="checkbox"
                        name="answer[anonymous]"
                        id="field-anonymous"
                        value="1"
                        <?php echo $anonChecked; ?>
                    />
                    <label for="field-anonymous">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS'); ?>
                    </label>
                </div>
                <div class="input-wrap">
                    <label for="field-question">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_QUESTION'); ?>
                    </label>
                    <input
                        type="text"
                        id="field-question"
                        disabled="disabled"
                        readonly="readonly"
                        value="<?php echo $questionVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="field-answer">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_ANSWER'); ?>
                        <span class="required">
                            <?php echo Lang::txt('JOPTION_REQUIRED'); ?>
                        </span>
                    </label>
                    <?php
                    echo $this->editor(
                        'answer[answer]',
                        $this->escape($this->row->get('answer')),
                        50,
                        15,
                        'field-answer',
                        array(
                            'class' => 'required minimal no-footer',
                            'buttons' => false,
                        )
                    );
                    ?>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th><?php echo Lang::txt('COM_ANSWERS_FIELD_ID'); ?>:</th>
                        <td><?php echo $this->row->get('id'); ?></td>
                    </tr>
                <?php if ($this->row->get('id')) { ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_ANSWERS_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo Date::of($this->row->get('created'))->toLocal(); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_ANSWERS_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape(
                                stripslashes($this->row->creator->get('name'))
                            );
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_ANSWERS_FIELD_HELPFUL'); ?>:</th>
                        <td>
                            <span class="votes up">
                                +<?php echo $this->row->get('helpful'); ?>
                            </span>
                            <span class="votes down">
                                -<?php echo $this->row->get('nothelpful'); ?>
                            </span>
                            <?php
                            $hasVotes = $this->row->get('helpful') > 0
                                || $this->row->get('nothelpful') > 0;
                            if ($hasVotes) {
                                $confirmTxt = Lang::txt(
                                    "COM_ANSWERS_CONFIRM_RESET"
                                );
                                $resetVal = Lang::txt(
                                    'COM_ANSWERS_FIELD_RESET'
                                );
                                ?>
                                <input
                                    type="button"
                                    name="reset_helpful"
                                    id="reset_helpful"
                                    value="<?php echo $resetVal; ?>"
                                    data-confirm="<?php echo $confirmTxt; ?>"
                                />
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <fieldset class="adminform">
                <legend>
                    <span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span>
                </legend>

                <div class="input-wrap">
                    <label for="field-state">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_ACCEPT'); ?>
                    </label><br />
                    <input
                        type="checkbox"
                        name="answer[state]"
                        id="field-state"
                        value="1"
                        <?php echo $stateChecked; ?>
                    /> (<?php echo $stateLabel; ?>)
                </div>

                <div class="input-wrap">
                    <label for="field-created_by">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_CREATOR'); ?>
                    </label><br />
                    <input
                        type="text"
                        name="answer[created_by]"
                        id="field-created_by"
                        size="25"
                        maxlength="50"
                        value="<?php echo $createdByVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-created">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_CREATED'); ?>
                    </label><br />
                    <?php
                    echo Html::input(
                        'calendar',
                        'answer[created]',
                        $this->row->get('created', Date::toSql()),
                        array('id' => 'field-created')
                    );
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

    <input
        type="hidden"
        name="answer[question_id]"
        value="<?php echo $this->question->get('id'); ?>"
    />
    <input
        type="hidden"
        name="answer[id]"
        value="<?php echo $this->row->get('id'); ?>"
    />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
