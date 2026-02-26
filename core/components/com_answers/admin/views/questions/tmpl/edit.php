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
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Answers\Helpers\Permissions::getActions('question');

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
Toolbar::help('question');

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
$emailChecked = ($this->row->get('email'))
    ? 'checked="checked"'
    : '';
$subjectVal = $this->escape($this->row->get('subject'));
$tagsVal = $this->escape(
    stripslashes($this->row->tags('string'))
);
$createdByVal = $this->row->get('created_by', User::get('id'));
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

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <input
                                type="checkbox"
                                name="question[anonymous]"
                                id="field-anonymous"
                                value="1"
                                <?php echo $anonChecked; ?>
                            />
                            <label for="field-anonymous">
                                <?php echo Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <input
                                type="checkbox"
                                name="question[email]"
                                id="field-email"
                                value="1"
                                <?php echo $emailChecked; ?>
                            />
                            <label for="field-email">
                                <?php echo Lang::txt('COM_ANSWERS_FIELD_NOTIFY'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <label for="field-subject">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_SUBJECT'); ?>:
                        <span class="required">
                            <?php echo Lang::txt('JOPTION_REQUIRED'); ?>
                        </span>
                    </label><br />
                    <input
                        type="text"
                        name="question[subject]"
                        id="field-subject"
                        class="required"
                        maxlength="250"
                        value="<?php echo $subjectVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-question">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_QUESTION'); ?>:
                    </label><br />
                    <?php
                    echo $this->editor(
                        'question[question]',
                        $this->escape($this->row->get('question')),
                        50,
                        15,
                        'field-question',
                        array(
                            'class' => 'minimal no-footer',
                            'buttons' => false,
                        )
                    );
                    ?>
                </div>

                <div
                    class="input-wrap"
                    data-hint="<?php echo Lang::txt('COM_ANSWERS_FIELD_TAGS_HINT'); ?>"
                >
                    <label for="field-tags">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_TAGS'); ?>:
                        <span class="required">
                            <?php echo Lang::txt('JOPTION_REQUIRED'); ?>
                        </span>
                    </label><br />
                    <textarea
                        name="question[tags]"
                        id="field-tags"
                        class="required"
                        cols="50"
                        rows="3"
                    ><?php echo $tagsVal; ?></textarea>
                    <span class="hint">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_TAGS_HINT'); ?>
                    </span>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th><?php echo Lang::txt('COM_ANSWERS_FIELD_ID'); ?>:</th>
                        <td>
                            <?php echo $this->row->get('id', 0); ?>
                            <input
                                type="hidden"
                                name="question[id]"
                                value="<?php echo $this->row->get('id'); ?>"
                            />
                        </td>
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
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_ANSWERS_PARAMETERS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-created_by">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_CREATOR'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="question[created_by]"
                        id="field-created_by"
                        size="25"
                        maxlength="50"
                        value="<?php echo $createdByVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-created">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_CREATED'); ?>:
                    </label><br />
                    <?php
                    echo Html::input(
                        'calendar',
                        'question[created]',
                        $this->row->get('created', Date::toSql()),
                        array('id' => 'field-created')
                    );
                    ?>
                </div>

                <div class="input-wrap">
                    <label for="field-state">
                        <?php echo Lang::txt('COM_ANSWERS_FIELD_STATE'); ?>:
                    </label><br />
                    <select name="question[state]" id="field-state">
                        <?php
                        $openSel = ($this->row->get('state') == 0)
                            ? ' selected="selected"'
                            : '';
                        $closedSel = ($this->row->get('state') == 1)
                            ? ' selected="selected"'
                            : '';
                        ?>
                        <option value="0"<?php echo $openSel; ?>>
                            <?php echo Lang::txt('COM_ANSWERS_STATE_OPEN'); ?>
                        </option>
                        <option value="1"<?php echo $closedSel; ?>>
                            <?php echo Lang::txt('COM_ANSWERS_STATE_CLOSED'); ?>
                        </option>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
