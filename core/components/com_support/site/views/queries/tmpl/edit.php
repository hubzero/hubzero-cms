<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$tmpl = Request::getString('tmpl', '');
$no_html = Request::getInt('no_html', 0);

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
    $formAction = Route::url('index.php?option=' . $this->option);
    $missingTitleErr = Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE');
    $rowTitle = $this->escape(stripslashes($this->row->title));
    $rowConditions = $this->escape(stripslashes($this->row->conditions));
    $noHtmlVal = ($tmpl) ? 1 : Request::getInt('no_html', 0);
    ?>
    <form
        action="<?php echo $formAction; ?>"
        method="post"
        name="adminForm"
        id="item-form"
    >
        <div class="col span12">
            <fieldset class="adminform">
                <legend><?php echo Lang::txt('JDETAILS'); ?></legend>

                <table class="admintable">
                    <tbody>
                        <tr>
                            <td class="key">
                                <label for="field-iscore">
                                    <?php echo Lang::txt('COM_SUPPORT_FIELD_TYPE'); ?>
                                </label>
                            </td>
                            <td colspan="2">
                                <select name="fields[iscore]" id="field-iscore">
                                    <optgroup label="<?php echo Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON'); ?>">
                                        <?php
                                        $sel2 = ($this->row->iscore == 2) ? ' selected="selected"' : '';
                                        $sel4 = ($this->row->iscore == 4) ? ' selected="selected"' : '';
                                        ?>
                                        <option value="2"<?php echo $sel2; ?>>
                                            <?php echo Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_ACL'); ?>
                                        </option>
                                        <option value="4"<?php echo $sel4; ?>>
                                            <?php echo Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_NO_ACL'); ?>
                                        </option>
                                    </optgroup>
                                    <?php
                                    $sel1 = ($this->row->iscore == 1) ? ' selected="selected"' : '';
                                    $sel0 = ($this->row->iscore == 0) ? ' selected="selected"' : '';
                                    ?>
                                    <option value="1"<?php echo $sel1; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_TYPE_MINE'); ?>
                                    </option>
                                    <option value="0"<?php echo $sel0; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_TYPE_CUSTOM'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <label for="field-title">
                                    <?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?>
                                </label>
                            </td>
                            <td colspan="2">
                                <input
                                    type="text"
                                    name="fields[title]"
                                    id="field-title"
                                    data-empty="<?php echo $missingTitleErr; ?>"
                                    value="<?php echo $rowTitle; ?>"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <fieldset class="query">
                                    <?php
                                    if ($this->row->conditions) {
                                        $condition = json_decode($this->row->conditions);
                                        //foreach ($conditions as $condition)
                                        //{
                                            $this->view('condition')
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
                            <td class="key">
                                <label for="field-sort">
                                    <?php echo Lang::txt('Sort results by:'); ?>
                                </label>
                            </td>
                            <td>
                                <select name="fields[sort]" id="field-sort">
                                    <?php
                                    $sortOpen     = ($this->row->sort == 'open') ? ' selected="selected"' : '';
                                    $sortStatus   = ($this->row->sort == 'status') ? ' selected="selected"' : '';
                                    $sortLogin    = ($this->row->sort == 'login') ? ' selected="selected"' : '';
                                    $sortOwner    = ($this->row->sort == 'owner') ? ' selected="selected"' : '';
                                    $sortGroup    = ($this->row->sort == 'group') ? ' selected="selected"' : '';
                                    $sortId       = ($this->row->sort == 'id') ? ' selected="selected"' : '';
                                    $sortReport   = ($this->row->sort == 'report') ? ' selected="selected"' : '';
                                    $sortSeverity = ($this->row->sort == 'severity') ? ' selected="selected"' : '';
                                    $sortTag      = ($this->row->sort == 'tag') ? ' selected="selected"' : '';
                                    $sortType     = ($this->row->sort == 'type') ? ' selected="selected"' : '';
                                    $sortCreated  = ($this->row->sort == 'created') ? ' selected="selected"' : '';
                                    $sortClosed   = ($this->row->sort == 'closed') ? ' selected="selected"' : '';
                                    $sortCategory = ($this->row->sort == 'category') ? ' selected="selected"' : '';
                                    ?>
                                    <option value="open"<?php echo $sortOpen; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN'); ?>
                                    </option>
                                    <option value="status"<?php echo $sortStatus; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS'); ?>
                                    </option>
                                    <option value="login"<?php echo $sortLogin; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER'); ?>
                                    </option>
                                    <option value="owner"<?php echo $sortOwner; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER'); ?>
                                    </option>
                                    <option value="group"<?php echo $sortGroup; ?>>
                                        <?php echo Lang::txt('Group'); ?>
                                    </option>
                                    <option value="id"<?php echo $sortId; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_ID'); ?>
                                    </option>
                                    <option value="report"<?php echo $sortReport; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT'); ?>
                                    </option>
                                    <?php /*
                                    <option value="resolved"...>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?>
                                    </option>
                                    */ ?>
                                    <option value="severity"<?php echo $sortSeverity; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY'); ?>
                                    </option>
                                    <option value="tag"<?php echo $sortTag; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TAG'); ?>
                                    </option>
                                    <option value="type"<?php echo $sortType; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE'); ?>
                                    </option>
                                    <option value="created"<?php echo $sortCreated; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED'); ?>
                                    </option>
                                    <option value="closed"<?php echo $sortClosed; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED'); ?>
                                    </option>
                                    <option value="category"<?php echo $sortCategory; ?>>
                                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY'); ?>
                                    </option>
                                </select>
                            </td>
                            <td>
                                <select name="fields[sort_dir]" id="field-sort_dir">
                                    <?php
                                    $sortDirDesc = (strtolower($this->row->sort_dir) == 'desc')
                                        ? ' selected="selected"' : '';
                                    $sortDirAsc  = (strtolower($this->row->sort_dir) == 'asc')
                                        ? ' selected="selected"' : '';
                                    ?>
                                    <option value="DESC"<?php echo $sortDirDesc; ?>>desc</option>
                                    <option value="ASC"<?php echo $sortDirAsc; ?>>asc</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
        <input
            type="hidden"
            name="fields[conditions]"
            id="field-conditions"
            value="<?php echo $rowConditions; ?>"
        />
        <input type="hidden" name="fields[user_id]" value="<?php echo User::get('id'); ?>" />

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="no_html" value="<?php echo $noHtmlVal; ?>" />
        <input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
        <input type="hidden" name="task" value="save" />

        <?php echo Html::input('token'); ?>
    </form>
    <?php
} else {
    if ($this->row->iscore != 0) {
        $this->row->title .= ' ' . Lang::txt('(copy)');
    }
    $formAction2     = Route::url('index.php?option=' . $this->option);
    $missingTitleErr = Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE');
    $rowTitle        = $this->escape(stripslashes($this->row->title));
    $rowConditions   = $this->escape(stripslashes($this->row->conditions));
    $rowIdVal        = ($this->row->iscore == 0) ? $this->row->id : 0;
    $noHtmlVal2      = ($tmpl) ? 1 : Request::getInt('no_html', 0);
    ?>
    <form
        action="<?php echo $formAction2; ?>"
        method="post"
        name="adminForm"
        id="queryForm"
    >
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
            <input
                type="text"
                name="fields[title]"
                id="field-title"
                data-empty="<?php echo $missingTitleErr; ?>"
                value="<?php echo $rowTitle; ?>"
            />
        </fieldset>

        <fieldset class="query">
            <?php
            if ($this->row->conditions) {
                $condition = json_decode($this->row->conditions);
                //foreach ($conditions as $condition)
                //{
                    $this->view('condition')
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
                <label for="field-sort"><?php echo Lang::txt('In folder'); ?></label>
                <select name="fields[folder_id]" id="field-folder_id">
                    <?php
                    include_once Component::path('com_support') . DS . 'models' . DS . 'queryfolder.php';

                    $folders = \Components\Support\Models\QueryFolder::all()
                        ->whereEquals('user_id', User::get('id'))
                        ->order('ordering', 'ASC')
                        ->rows();

                    if ($folders) {
                        foreach ($folders as $folder) {
                            $fldSel   = ($this->row->folder_id == $folder->id)
                                ? ' selected="selected"' : '';
                            $fldTitle = $this->escape(stripslashes($folder->title));
                            ?><option value="<?php echo $folder->id; ?>"<?php echo $fldSel; ?>><?php
                                echo $fldTitle;
?></option><?php
                        }
                    }
                    ?>
                </select>

                <label for="field-sort"><?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_BY'); ?></label>
                <select name="fields[sort]" id="field-sort">
                    <?php
                    $sortOpen     = ($this->row->sort == 'open') ? ' selected="selected"' : '';
                    $sortStatus   = ($this->row->sort == 'status') ? ' selected="selected"' : '';
                    $sortLogin    = ($this->row->sort == 'login') ? ' selected="selected"' : '';
                    $sortOwner    = ($this->row->sort == 'owner') ? ' selected="selected"' : '';
                    $sortGroup    = ($this->row->sort == 'group') ? ' selected="selected"' : '';
                    $sortId       = ($this->row->sort == 'id') ? ' selected="selected"' : '';
                    $sortReport   = ($this->row->sort == 'report') ? ' selected="selected"' : '';
                    $sortSeverity = ($this->row->sort == 'severity') ? ' selected="selected"' : '';
                    $sortTag      = ($this->row->sort == 'tag') ? ' selected="selected"' : '';
                    $sortType     = ($this->row->sort == 'type') ? ' selected="selected"' : '';
                    $sortCreated  = ($this->row->sort == 'created') ? ' selected="selected"' : '';
                    $sortClosed   = ($this->row->sort == 'closed') ? ' selected="selected"' : '';
                    $sortCategory = ($this->row->sort == 'category') ? ' selected="selected"' : '';
                    ?>
                    <option value="open"<?php echo $sortOpen; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN'); ?>
                    </option>
                    <option value="status"<?php echo $sortStatus; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS'); ?>
                    </option>
                    <option value="login"<?php echo $sortLogin; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER'); ?>
                    </option>
                    <option value="owner"<?php echo $sortOwner; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER'); ?>
                    </option>
                    <option value="group"<?php echo $sortGroup; ?>>
                        <?php echo Lang::txt('Group'); ?>
                    </option>
                    <option value="id"<?php echo $sortId; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_ID'); ?>
                    </option>
                    <option value="report"<?php echo $sortReport; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT'); ?>
                    </option>
                    <?php /*
                    <option value="resolved"...>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_RESOLUTION'); ?>
                    </option>
                    */ ?>
                    <option value="severity"<?php echo $sortSeverity; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY'); ?>
                    </option>
                    <option value="tag"<?php echo $sortTag; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TAG'); ?>
                    </option>
                    <option value="type"<?php echo $sortType; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE'); ?>
                    </option>
                    <option value="created"<?php echo $sortCreated; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED'); ?>
                    </option>
                    <option value="closed"<?php echo $sortClosed; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED'); ?>
                    </option>
                    <option value="category"<?php echo $sortCategory; ?>>
                        <?php echo Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY'); ?>
                    </option>
                </select>
                <select name="fields[sort_dir]" id="field-sort_dir">
                    <?php
                    $sortDirDesc = (strtolower($this->row->sort_dir) == 'desc')
                        ? ' selected="selected"' : '';
                    $sortDirAsc  = (strtolower($this->row->sort_dir) == 'asc')
                        ? ' selected="selected"' : '';
                    ?>
                    <option value="DESC"<?php echo $sortDirDesc; ?>>desc</option>
                    <option value="ASC"<?php echo $sortDirAsc; ?>>asc</option>
                </select>
            </p>
        </fieldset>

        <input type="hidden" name="fields[id]" value="<?php echo $rowIdVal; ?>" />
        <input
            type="hidden"
            name="fields[conditions]"
            id="field-conditions"
            value="<?php echo $rowConditions; ?>"
        />
        <input type="hidden" name="fields[user_id]" value="<?php echo User::get('id'); ?>" />

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="no_html" value="<?php echo $noHtmlVal2; ?>" />
        <input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
        <input type="hidden" name="task" value="save" />

        <?php echo Html::input('token'); ?>
        </fieldset>
    </form>
<?php }
