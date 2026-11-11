<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Wiki\Helpers\Permissions::getActions('page');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_PAGE') . ': ' . $text, 'wiki');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('page');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

// The attribute that marks the current value in the form fields below
$modeSelected = function ($value) {
    if ($this->row->param('mode') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$hideAuthorsChecked = function ($value) {
    if ($this->row->param('hide_authors') == $value) {
        return ' checked="checked"';
    }

    return '';
};

$allowChangesChecked = function ($value) {
    if ($this->row->param('allow_changes') == $value) {
        return ' checked="checked"';
    }

    return '';
};

$allowCommentsChecked = function ($value) {
    if ($this->row->param('allow_comments') == $value) {
        return ' checked="checked"';
    }

    return '';
};

$stateSelected = function ($value) {
    if ($this->row->get('state') == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="item-form" class="editform form-validate"
    data-invalid-msg="<?= $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')) ?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="pagetitle"><?= Lang::txt('COM_WIKI_FIELD_TITLE') ?>: <span
                        class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label><br />
                    <input type="text" name="page[title]" id="pagetitle" class="required" size="30" maxlength="255"
                        value="<?= $this->escape(stripslashes($this->row->get('title', ''))) ?>" />
                </div>

                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_WIKI_FIELD_PAGENAME_HINT'); ?>">
                    <label for="pagename"><?= Lang::txt('COM_WIKI_FIELD_PAGENAME') ?>: <span
                        class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label><br />
                    <input type="text" name="page[pagename]" id="pagename" class="required" size="30" maxlength="255"
                        value="<?= $this->escape(stripslashes($this->row->get('pagename', ''))) ?>" />
                    <span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_PAGENAME_HINT'); ?></span>
                </div>

                <div class="input-wrap">
                    <label for="field-path"><?php echo Lang::txt('COM_WIKI_FIELD_PATH'); ?>:</label><br />
                    <input type="text" name="page[path]" id="field-path" size="30" maxlength="255"
                        value="<?= $this->escape(stripslashes($this->row->get('path', ''))) ?>" />
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <label for="field-scope"><?php echo Lang::txt('COM_WIKI_FIELD_SCOPE'); ?>:</label><br />
                            <input type="text" name="page[scope]" id="field-scope" size="30" maxlength="255"
                                value="<?= $this->escape(stripslashes($this->row->get('scope', ''))) ?>" />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <label for="field-scope_id"><?= Lang::txt('COM_WIKI_FIELD_SCOPE_ID') ?>:</label><br />
                            <input type="text" name="page[scope_id]" id="field-scope_id" size="30" maxlength="255"
                                value="<?= $this->escape(stripslashes($this->row->get('scope_id', ''))) ?>" />
                        </div>
                    </div>
                </div>

                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_WIKI_FIELD_AUTHORS_HINT'); ?>">
                    <label for="pageauthors"><?php echo Lang::txt('COM_WIKI_FIELD_AUTHORS'); ?>:</label><br />
                    <textarea name="page[authors]" id="pageauthors" cols="35" rows="3"><?php
                        $authors = [];
                    foreach ($this->row->authors()->rows() as $author) {
                        $authors[] = $author->user()->get('username');
                    }
                        echo $this->escape(implode(', ', $authors));
                    ?></textarea>
                    <span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_AUTHORS_HINT'); ?></span>
                </div>

                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_WIKI_FIELD_TAGS_HINT'); ?>">
                    <label for="field-tags"><?php echo Lang::txt('COM_WIKI_FIELD_TAGS'); ?>:</label><br />
                    <textarea name="page[tags]" id="field-tags" cols="35"
                        rows="3"><?= $this->escape(stripslashes($this->row->tags('string'))) ?></textarea>
                    <span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_TAGS_HINT'); ?></span>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_WIKI_FIELD_ID'); ?></th>
                        <td><?php echo $this->escape($this->row->get('id')); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_WIKI_FIELD_CREATED'); ?></th>
                        <td><?= $this->escape($this->row->created('time') . ' ' . $this->row->created('date')) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_WIKI_FIELD_CREATOR'); ?></th>
                        <td><?= $this->escape(stripslashes($this->row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')))) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_WIKI_FIELD_HITS'); ?></th>
                        <td><?php echo $this->escape($this->row->get('hits')); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_WIKI_FIELD_REVISIONS'); ?></th>
                        <td><?php echo $this->row->versions()->total(); ?></td>
                    </tr>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_WIKI_FIELDSET_PARAMETERS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="param-mode"><?php echo Lang::txt('COM_WIKI_MODE_LABEL'); ?>:</label><br />
                    <select name="params[mode]" id="param-mode">
                        <option value="wiki"<?= $modeSelected('wiki') ?>><?= Lang::txt('COM_WIKI_MODE_WIKI') ?></option>
                        <option value="knol"<?= $modeSelected('knol') ?>><?= Lang::txt('COM_WIKI_MODE_KNOL') ?></option>
                        <option value="static"<?= $modeSelected('static') ?>><?= Lang::txt('COM_WIKI_MODE_STATIC') ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <input type="checkbox" name="params[hide_authors]"
                        id="param-hide_authors"<?= $hideAuthorsChecked(1) ?> value="1" />
                    <label for="param-hide_authors"><?php echo Lang::txt('COM_WIKI_FIELD_HIDE_AUTHORS'); ?></label>
                </div>

                <div class="input-wrap">
                    <input type="checkbox" name="params[allow_changes]"
                        id="param-allow_changes"<?= $allowChangesChecked(1) ?> value="1" />
                    <label for="param-allow_changes"><?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_CHANGES'); ?></label>
                </div>

                <div class="input-wrap">
                    <input type="checkbox" name="params[allow_comments]"
                        id="param-allow_comments"<?= $allowCommentsChecked(1) ?> value="1" />
                    <label for="param-allow_comments"><?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_COMMENTS'); ?></label>
                </div>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_WIKI_FIELD_STATE'); ?>:</label><br />
                    <select name="page[state]" id="field-state">
                        <option value="0"<?= $stateSelected(0) ?>><?= Lang::txt('JUNPUBLISHED') ?></option>
                        <option value="1"<?= $stateSelected(1) ?>><?= Lang::txt('JPUBLISHED') ?></option>
                        <option value="2"<?= $stateSelected(2) ?>><?= Lang::txt('COM_WIKI_STATE_TRASHED') ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <label for="field-access"><?php echo Lang::txt('COM_WIKI_FIELD_ACCESS'); ?>:</label><br />
                    <select name="page[access]" id="field-access">
                        <?= Html::select(
                            'options',
                            Html::access('assetgroups'),
                            'value',
                            'text',
                            $this->row->get('access'),
                        ) ?>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="page[id]" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
