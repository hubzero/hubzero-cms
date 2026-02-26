<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', true);

$canDo = \Components\Redirect\Helpers\Redirect::getActions();

Toolbar::title(Lang::txt('COM_REDIRECT_MANAGER_LINK'), 'redirect');
// If not checked out, can save the item.
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
}
// This component does not support Save as Copy due to uniqueness checks.
// While it can be done, it causes too much confusion if the user does
// not change the Old URL.
if ($canDo->get('core.edit') && $canDo->get('core.create')) {
    Toolbar::save2new();
}

if (empty($this->item->id)) {
    Toolbar::spacer();
    Toolbar::cancel('cancel');
} else {
    Toolbar::spacer();
    Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
}
Toolbar::spacer();
Toolbar::help('link');

// Include the HTML helpers.
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&task=edit&id=' . (int) $this->row->id
);
$invalidMsg = $this->escape(
    Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')
);
$legendTxt = $this->row->isNew()
    ? Lang::txt('COM_REDIRECT_NEW_LINK')
    : Lang::txt('COM_REDIRECT_EDIT_LINK', $this->row->id);
$oldUrlDesc = Lang::txt('COM_REDIRECT_FIELD_OLD_URL_DESC');
$oldUrlLabel = Lang::txt('COM_REDIRECT_FIELD_OLD_URL_LABEL');
$newUrlDesc = Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC');
$newUrlLabel = Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL');
$requiredTxt = Lang::txt('JOPTION_REQUIRED');
$commentDesc = Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC');
$commentLabel = Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL');
$publishedDesc = Lang::txt('JFIELD_PUBLISHED_DESC');
$oldUrlVal = $this->escape($this->row->old_url);
$newUrlVal = $this->escape($this->row->new_url);
$commentVal = $this->escape($this->row->comment);
$idVal = $this->escape($this->row->id);
$createdVal = $this->escape($this->row->created_date);
$modifiedVal = $this->escape($this->row->modified_date);
$hitsVal = $this->escape($this->row->hits);

$statusCode = $this->row->status_code;
$hasNewUrl = $this->row->new_url;
$sel404 = ((!$hasNewUrl && !$statusCode) || $statusCode == 404)
    ? ' selected="selected"' : '';
$sel301 = ($statusCode == 301)
    ? ' selected="selected"' : '';
$sel302 = (($hasNewUrl && !$statusCode) || $statusCode == 302)
    ? ' selected="selected"' : '';
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo $legendTxt; ?></span></legend>

                <div class="input-wrap" data-hint="<?php echo $oldUrlDesc; ?>">
                    <label id="fields-old_url-lbl" for="fields-old_url">
                        <?php echo $oldUrlLabel; ?>
                        <span class="required">
                            <?php echo $requiredTxt; ?>
                        </span>
                    </label>
                    <input
                        type="text"
                        name="fields[old_url]"
                        id="fields-old_url"
                        value="<?php echo $oldUrlVal; ?>"
                        class="inputbox required"
                        maxlength="255"
                    />
                    <span class="hint"><?php echo $oldUrlDesc; ?></span>
                </div>

                <div class="input-wrap" data-hint="<?php echo $newUrlDesc; ?>">
                    <label id="fields-new_url-lbl" for="fields-new_url">
                        <?php echo $newUrlLabel; ?>
                        <span class="required">
                            <?php echo $requiredTxt; ?>
                        </span>
                    </label>
                    <input
                        type="text"
                        name="fields[new_url]"
                        id="fields-new_url"
                        value="<?php echo $newUrlVal; ?>"
                        class="inputbox required"
                        maxlength="255"
                    />
                    <span class="hint"><?php echo $newUrlDesc; ?></span>
                </div>

                <div class="input-wrap" data-hint="<?php echo $publishedDesc; ?>">
                    <label for="fields-status_code">
                        <?php echo Lang::txt('COM_REDIRECT_STATUS'); ?>
                    </label>
                    <select name="fields[status_code]" id="fields-status_code">
                        <option value="404"<?php echo $sel404; ?>>
                            <?php echo Lang::txt('COM_REDIRECT_STATUS_NOTFOUND'); ?>
                        </option>
                        <option value="301"<?php echo $sel301; ?>>
                            <?php echo Lang::txt('COM_REDIRECT_STATUS_PERMANENT'); ?>
                        </option>
                        <option value="302"<?php echo $sel302; ?>>
                            <?php echo Lang::txt('COM_REDIRECT_STATUS_FOUND'); ?>
                        </option>
                    </select>
                </div>

                <div class="input-wrap" data-hint="<?php echo $commentDesc; ?>">
                    <label id="fields-comment-lbl" for="fields-comment">
                        <?php echo $commentLabel; ?>
                    </label>
                    <input
                        type="text"
                        name="fields[comment]"
                        id="fields-comment"
                        value="<?php echo $commentVal; ?>"
                        class="inputbox"
                    />
                    <span class="hint"><?php echo $commentDesc; ?></span>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th><?php echo Lang::txt('JGLOBAL_FIELD_ID_LABEL'); ?></th>
                        <td>
                            <?php echo $idVal; ?>
                            <input
                                type="hidden"
                                name="fields[id]"
                                id="fields-id"
                                value="<?php echo $idVal; ?>"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo Lang::txt('COM_REDIRECT_FIELD_CREATED_DATE_LABEL'); ?>
                        </th>
                        <td>
                            <?php echo $createdVal; ?>
                            <input
                                type="hidden"
                                name="fields[created_date]"
                                id="fields-created_date"
                                value="<?php echo $createdVal; ?>"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo Lang::txt('COM_REDIRECT_FIELD_UPDATED_DATE_LABEL'); ?>
                        </th>
                        <td>
                            <?php echo $modifiedVal; ?>
                            <input
                                type="hidden"
                                name="fields[modified_date]"
                                id="fields-modified_date"
                                value="<?php echo $modifiedVal; ?>"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('JGLOBAL_HITS'); ?></th>
                        <td>
                            <?php echo $hitsVal; ?>
                            <input
                                type="hidden"
                                name="fields[hits]"
                                id="fields-hits"
                                value="<?php echo $hitsVal; ?>"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_REDIRECT_OPTIONS'); ?></span></legend>

                <div class="input-wrap" data-hint="<?php echo $publishedDesc; ?>">
                    <label id="fields-published-lbl" for="fields-published">
                        <?php echo Lang::txt('JSTATUS'); ?>
                        <span class="required">
                            <?php echo $requiredTxt; ?>
                        </span>
                    </label>
                    <select name="fields[published]" id="fields-published">
                        <option value="1"<?php if ($this->row->published == 1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('JENABLED'); ?></option>
                        <option value="0"<?php if ($this->row->published == 0) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('JDISABLED'); ?></option>
                        <option value="2"<?php if ($this->row->published == 2) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('JARCHIVED'); ?></option>
                        <option value="-2"<?php if ($this->row->published == -2) {
                            echo ' selected="selected"';
                                          } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />

    <?php echo Html::input('token'); ?>
</form>
