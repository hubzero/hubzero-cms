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
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

$tmpl = Request::getString('tmpl', '');
$no_html = Request::getInt('no_html', 0);

// The attribute that marks the current value in the form fields below
$iscoreSelected = function ($value) {
    if ($this->row->iscore == $value) {
        return ' selected="selected"';
    }

    return '';
};

$sortSelected = function ($value) {
    if ($this->row->sort == $value) {
        return ' selected="selected"';
    }

    return '';
};

$folderIdSelected = function ($value) {
    if ($this->row->folder_id == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

    <script type="application/json" id="conditions-data">
    {
        "conditions": <?php echo json_encode($this->conditions); ?>
    }
    </script>

<?php
if (!$tmpl && !$no_html) {
    // Push some styles to the template
    $this->js('json2.js');
    $this->js('condition.builder.js');
    $this->css('conditions.css');
    ?>
    <form action="<?= Route::url('index.php?option=' . $this->option) ?>" method="post" name="adminForm" id="item-form">
        <div class="col span12">
            <fieldset class="adminform">
                <legend><?php echo Lang::txt('JDETAILS'); ?></legend>

                <table class="admintable">
                    <tbody>
                        <tr>
                            <td class="key"><label
                                for="field-iscore"><?= Lang::txt('COM_SUPPORT_FIELD_TYPE') ?></label></td>
                            <td colspan="2">
                                <select name="fields[iscore]" id="field-iscore">
                                    <optgroup label="<?php echo Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON'); ?>">
                                        <option value="2"<?= $iscoreSelected(2) ?>><?= Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_ACL') ?></option>
                                        <option value="4"<?= $iscoreSelected(4) ?>><?= Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_NO_ACL') ?></option>
                                    </optgroup>
                                    <option value="1"<?= $iscoreSelected(1) ?>><?= Lang::txt('COM_SUPPORT_QUERY_TYPE_MINE') ?></option>
                                    <option value="0"<?= $iscoreSelected(0) ?>><?= Lang::txt('COM_SUPPORT_QUERY_TYPE_CUSTOM') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="key"><label
                                for="field-title"><?= Lang::txt('COM_SUPPORT_FIELD_TITLE') ?></label></td>
                            <td colspan="2"><input type="text" name="fields[title]" id="field-title"
                                data-empty="<?= Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE') ?>"
                                value="<?= $this->escape(stripslashes($this->row->title)) ?>" /></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <fieldset class="query">
                                    <?php
                                    if ($this->row->conditions) {
                                        $condition = json_decode($this->row->conditions);
                                        //foreach ($conditions as $condition)
                                        //{
                                            $this
                                                 ->view('condition')
                                                 ->set('option', $this->option)
                                                 ->set('controller', $this->controller)
                                                 ->set('condition', $condition)
                                                 ->set('conditions', $this->conditions)
                                                 ->set('row', $this->row)
                                                 ->display();
                                        //}
                                    }
                                    ?>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td class="key"><label for="field-sort"><?= Lang::txt('Sort results by:') ?></label></td>
                            <td>
                                <select name="fields[sort]" id="field-sort">
                                    <option value="open"<?= $sortSelected('open') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') ?></option>
                                    <option value="status"<?= $sortSelected('status') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') ?></option>
                                    <option value="login"<?= $sortSelected('login') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') ?></option>
                                    <option value="owner"<?= $sortSelected('owner') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') ?></option>
                                    <option
                                        value="group"<?= $sortSelected('group') ?>><?= Lang::txt('Group') ?></option>
                                    <option value="id"<?= $sortSelected('id') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_ID') ?></option>
                                    <option value="report"<?= $sortSelected('report') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') ?></option>
                                    <?php /*<option value="resolved"<?php if ($this->row->sort == 'resolved') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?></option>*/ ?>
                                    <option value="severity"<?= $sortSelected('severity') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') ?></option>
                                    <option value="tag"<?= $sortSelected('tag') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') ?></option>
                                    <option value="type"<?= $sortSelected('type') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') ?></option>
                                    <option value="created"<?= $sortSelected('created') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') ?></option>
                                    <option value="closed"<?= $sortSelected('closed') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') ?></option>
                                    <option value="category"<?= $sortSelected('category') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') ?></option>
                                </select>
                            </td>
                            <td>
                                <select name="fields[sort_dir]" id="field-sort_dir"
                                    aria-label="<?= Lang::txt('COM_SUPPORT_QUERY_SORT_DIRECTION') ?>">
                                    <option value="DESC"<?= strtolower($this->row->sort_dir) == 'desc' ? ' selected="selected"' : '' ?>>desc</option>
                                    <option value="ASC"<?= strtolower($this->row->sort_dir) == 'asc' ? ' selected="selected"' : '' ?>>asc</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
        <input type="hidden" name="fields[conditions]" id="field-conditions"
            value="<?= $this->escape(stripslashes($this->row->conditions)) ?>" />
        <input type="hidden" name="fields[user_id]" value="<?php echo User::get('id'); ?>" />

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="no_html" value="<?php echo ($tmpl) ? 1 : Request::getInt('no_html', 0); ?>" />
        <input type="hidden" name="tmpl" value="<?php echo $this->escape($tmpl); ?>" />
        <input type="hidden" name="task" value="save" />

        <?php echo Html::input('token'); ?>
    </form>
    <?php
} else {
    if ($this->row->iscore != 0) {
        $this->row->title .= ' ' . Lang::txt('(copy)');
    }
    ?>
    <form action="<?= Route::url('index.php?option=' . $this->option) ?>" method="post" name="adminForm" id="queryForm">
        <h3>
            <span class="configuration-options">
                <input type="submit" value="<?php echo Lang::txt('COM_SUPPORT_QUERY_SAVE'); ?>" />
            </span>
            <span class="configuration">
                <?php echo Lang::txt('COM_SUPPORT_QUERY_BUILDER') ?>
            </span>
        </h3>
        <fieldset class="wrapper">

        <fieldset class="fields title">
            <label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?></label>
            <input type="text" name="fields[title]" id="field-title"
                data-empty="<?= Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE') ?>"
                value="<?= $this->escape(stripslashes($this->row->title)) ?>" />
        </fieldset>

        <fieldset class="query">
            <?php
            if ($this->row->conditions) {
                $condition = json_decode($this->row->conditions);
                //foreach ($conditions as $condition)
                //{
                    $this
                         ->view('condition')
                         ->set('option', $this->option)
                         ->set('controller', $this->controller)
                         ->set('condition', $condition)
                         ->set('conditions', $this->conditions)
                         ->set('row', $this->row)
                         ->display();

                //}
            }
            ?>
        </fieldset>

        <fieldset class="fields sort">
            <p>
                <label for="field-folder_id"><?php echo Lang::txt('In folder'); ?></label>
                <select name="fields[folder_id]" id="field-folder_id">
                    <?php
                    $folders = \Components\Support\Models\QueryFolder::all()
                        ->whereEquals('user_id', User::get('id'))
                        ->order('ordering', 'ASC')
                        ->rows();

                    if ($folders) {
                        foreach ($folders as $folder) {
                            ?><option value="<?= $folder->id ?>"<?= $folderIdSelected($folder->id) ?>><?= $this->escape(stripslashes($folder->title)) ?></option><?php
                        }
                    }
                    ?>
                </select>

                <label for="field-sort-inline"><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_BY'); ?></label>
                <select name="fields[sort]" id="field-sort-inline">
                    <option value="open"<?= $sortSelected('open') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') ?></option>
                    <option value="status"<?= $sortSelected('status') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') ?></option>
                    <option value="login"<?= $sortSelected('login') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') ?></option>
                    <option value="owner"<?= $sortSelected('owner') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') ?></option>
                    <option value="group"<?= $sortSelected('group') ?>><?= Lang::txt('Group') ?></option>
                    <option value="id"<?= $sortSelected('id') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_ID') ?></option>
                    <option value="report"<?= $sortSelected('report') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') ?></option>
                    <?php /*<option value="resolved"<?php if ($this->row->sort == 'resolved') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?></option>*/ ?>
                    <option value="severity"<?= $sortSelected('severity') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') ?></option>
                    <option
                        value="tag"<?= $sortSelected('tag') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') ?></option>
                    <option value="type"<?= $sortSelected('type') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') ?></option>
                    <option value="created"<?= $sortSelected('created') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') ?></option>
                    <option value="closed"<?= $sortSelected('closed') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') ?></option>
                    <option value="category"<?= $sortSelected('category') ?>><?= Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') ?></option>
                </select>
                <select name="fields[sort_dir]" id="field-sort_dir-inline"
                    aria-label="<?= Lang::txt('COM_SUPPORT_QUERY_SORT_DIRECTION') ?>">
                    <option value="DESC"<?= strtolower($this->row->sort_dir) == 'desc' ? ' selected="selected"' : '' ?>>desc</option>
                    <option value="ASC"<?= strtolower($this->row->sort_dir) == 'asc' ? ' selected="selected"' : '' ?>>asc</option>
                </select>
            </p>
        </fieldset>

        <input type="hidden" name="fields[id]" value="<?php echo ($this->row->iscore == 0) ? $this->row->id : 0; ?>" />
        <input type="hidden" name="fields[conditions]" id="field-conditions"
            value="<?= $this->escape(stripslashes($this->row->conditions)) ?>" />
        <input type="hidden" name="fields[user_id]" value="<?php echo User::get('id'); ?>" />

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="no_html" value="<?php echo ($tmpl) ? 1 : Request::getInt('no_html', 0); ?>" />
        <input type="hidden" name="tmpl" value="<?php echo $this->escape($tmpl); ?>" />
        <input type="hidden" name="task" value="save" />

        <?php echo Html::input('token'); ?>
        </fieldset>
    </form>
<?php }
