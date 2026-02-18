<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package     HUBzero CMS
 * @author      Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright   Copyright (c) 2012-2020 The Regents of the University of California.
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * Copyright (c) 2012-2020 The Regents of the University of California.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<div id="plg-header">
    <h3 class="databases"><?php echo Lang::txt('PLG_PROJECTS_DATABASES'); ?></h3>
</div>

<?php if ($this->model->access('content')) {
    $createUrl = Route::url(
        'index.php?option=' . $this->option
        . '&alias=' . $this->model->get('alias')
        . '&active=databases&action=create'
    ) . '#content';
    ?>
    <ul id="page_options" class="pluginOptions">
        <li>
            <a class="icon-add add btn"
                href="<?php echo $createUrl; ?>">
                <?php echo Lang::txt('PLG_PROJECTS_DATA_START'); ?>
            </a>
        </li>
    </ul>
<?php } ?>
<div id="confirm-file-delete"
    class="confirm-file-delete"
    title="<?php echo Lang::txt('PLG_PROJECTS_DATABASES_DELETE_PROJECT_DATABASE'); ?>">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0; color: red;"></span>
        <?php echo Lang::txt('Confirm database deletion.'); ?>
    </p>
</div>

<div id="prj-db-list">
    <?php if (count($this->list) > 0) { ?>
    <table class="listing">
        <thead>
            <tr>
                <th><?php echo Lang::txt('PLG_PROJECTS_DATABASES_TITLE'); ?></th>
                <th><?php echo Lang::txt('Source File'); ?></th>
                <th><?php echo Lang::txt('Created On'); ?></th>
                <th><?php echo Lang::txt('Created By'); ?></th>
                <?php if ($this->model->access('content')) {
                    $updateHelpTitle = Lang::txt(
                        'Click the \'Update\' link to recreate'
                        . ' a database using the updated source'
                        . ' CSV file from the file repository.'
                        . ' Click the help icon for more'
                        . ' information.'
                    );
                    ?>
                    <th>
                        <?php echo Lang::txt('Update Database'); ?>
                        <span class="update-help-icon"
                            title="<?php echo $updateHelpTitle; ?>">
                        </span>
                    </th>
                    <th>
                        <?php echo Lang::txt('PLG_PROJECTS_DATABASES_DELETE'); ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->list as $r) {
            if ($r['source_dir'] != '') {
                $full_path = htmlspecialchars($r['source_dir'] . DS . $r['source_file']);
            } else {
                $full_path = htmlspecialchars($r['source_file']);
            }

            $file_url = 'index.php?option=com_projects&alias=' . $this->model->get('alias')
                . '&active=files&action=download&subdir='
                . trim($r['source_dir'], '/') . '&asset=' . $r['source_file'];
            $file_url = Route::url($file_url);

            $file_name = '<a href="' . $file_url . '">' . $r['source_file'] . '</a>';

            $recreateUrl = Route::url(
                'index.php?option=com_projects&alias='
                . $this->model->get('alias')
                . '&active=databases&action=create&db_id='
                . $r['id']
            );
            $recreateTxt = Lang::txt('Update Database');
            $recreate = '<a href="' . $recreateUrl
                . '" class="re-create-db">'
                . $recreateTxt . '</a>';

            $file_extra = 'title="' . $full_path . '"';

            if ($r['source_revision'] != $r['source_revision_curr']) {
                if ($r['source_available']) {
                    $file_extra = 'class="file-updated" title="'
                        . Lang::txt('The file has changed, modified %s', $r['source_revision_date'])
                        . '"';
                } else {
                    $deletedMsg = Lang::txt(
                        'The file [%s] has been removed'
                        . ' or renamed %s',
                        $full_path,
                        $r['source_revision_date']
                    );
                    $file_extra = 'class="file-deleted"'
                        . ' title="' . $deletedMsg . '"';
                    $file_name = '<span style="color: #ddd;'
                        . ' cursor: not-allowed;">'
                        . $this->escape($r['source_file'])
                        . '</span>';
                    $restoreMsg = Lang::txt(
                        'The original file has been removed'
                        . ' or renamed, please restore the'
                        . ' file to enable this functionality'
                    );
                    $recreate = '<span title="'
                        . $restoreMsg
                        . '" style="color: #ddd;'
                        . ' cursor: not-allowed;">'
                        . Lang::txt('Recreate') . '<span>';
                }
            }
            ?>
            <tr class="mini faded">
                <td title="<?php echo $this->escape($r['description']); ?>"
                    data-db-title="<?php echo $this->escape($r['title']); ?>"
                    data-db-id="<?php echo $r['id']; ?>">
                    <?php
                    $viewerUrl = '/' . $this->dataviewer
                        . '/view/'
                        . $this->model->get('alias')
                        . ':dsl/' . $r['database_name'] . '/';
                    ?>
                    <a rel="noopener noreferrer"
                        target="_blank"
                        href="<?php echo $viewerUrl; ?>">
                        <?php echo $this->escape($r['title']); ?>
                    </a>
                <?php if ($this->model->access('content')) { ?>
                    <span class="db-update"
                        title="<?php echo Lang::txt('PLG_PROJECTS_DATABASES_CLICK_TO_EDIT'); ?>"></span>
                <?php } ?>
                </td>
                <td <?php  echo $file_extra; ?>>
                <?php  echo $file_name; ?>
                </td>
                <td>
                <?php echo $r['created']; ?>
                </td>
                <td>
                    <?php
                    $memberUrl = Route::url(
                        'index.php?option=com_members&id='
                        . $r['created_by']
                    );
                    ?>
                    <a href="<?php echo $memberUrl; ?>">
                        <?php echo $this->escape($r['name']); ?>
                    </a>
                </td>
            <?php if ($this->model->access('content')) { ?>
                <td>
                    <?php echo $recreate; ?>
                </td>
                <td>
                    <?php
                    $deleteUrl = Route::url(
                        'index.php?option=com_projects&alias='
                        . $this->model->get('alias')
                        . '&active=databases&action=delete&db_id='
                        . $r['id']
                    );
                    ?>
                    <a href="<?php echo $deleteUrl; ?>"
                        class="delete-db">
                        <?php echo Lang::txt('PLG_PROJECTS_DATABASES_DELETE'); ?>
                    </a>
                </td>
            <?php } ?>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <div id="update-db-help-dialog" title="How to update a database" style="display: none;">
        <strong>If you want to just update the way the data is displayed and do not want to change the data, skip to
        <span style="color: blue;">Step 4</span>.</strong>
        <h3 style="margin: 1.2em 0 .4em 0;">Step 1: Download the CSV file used for creating the database.</h3>
        <ul>
            <li>You can either use the "Source File" link or you can download the file from the Files section of the
            project</li>
            <li>Your spreadsheet will now have the DataStore information included in the first 3 rows</li>
        </ul>
        <h3 style="margin: 1.2em 0 .4em 0;">Step 2: Update the data in your spreadsheet.</h3>
        <ul>
            <li>You can add rows, delete rows and change data in rows</li>
            <li>Only change rows that are <strong>BELOW</strong> the <strong>DATASTART</strong> row</li>
            <li>Make sure your data matches the data types
            you defined for your columns,
            <br />the data types are listed in the
            2<sup>nd</sup> row for your convenience</li>
            <li>Save your file in the same CSV format</li>
        </ul>
        <h3 style="margin: 1.2em 0 .4em 0;">Step 3: Upload the updated CSV file to the project's File area .</h3>
        <ul>
            <li>Make sure to upload to the same folder. <br />Your uploaded file has to replace the CSV file that is
            there now</li>
        </ul>
        <h3 style="margin: 1.2em 0 .4em 0;">Step 4: Return to the Database area and click on "Update Database"</h3>
        <ul>
            <li>When you click "Update Database" the data from the updated spreadsheet will be loaded</li>
            <li>At this point you can customize the columns and change any of the column properties</li>
        </ul>
    </div>

        <?php
    } else {
        $noDataTxt = Lang::txt('PLG_PROJECTS_DATA_NO_DATA_FOUND');
        $out = '<p class="noresults">' . $noDataTxt;
        if ($this->model->access('content')) {
            $addUrl = Route::url(
                'index.php?option=' . $this->option
                . '&active=databases&alias='
                . $this->model->get('alias')
                . '&action=create#content'
            );
            $startTxt = Lang::txt('PLG_PROJECTS_DATA_START');
            $out .= ' <span class="addnew">'
                . '<a href="' . $addUrl . '" >'
                . $startTxt . '</a></span>';
        }
        $out .= '</p>';
        echo $out;
    }
    ?>
</div>
<?php
$updateFormUrl = Route::url(
    'index.php?option=' . $this->option
    . '&alias=' . $this->model->get('alias')
    . '&active=databases&action=update'
);
?>
<div id="prj-db-update-dialog"
    class="prj-db-update-dialog dialog"
    title="Update Title &amp; Description">
    <form id="prj-db-update-form"
        method="post"
        action="<?php echo $updateFormUrl; ?>">
        <input type="hidden" name="db_id" />
        <label><?php echo Lang::txt('PLG_PROJECTS_DATABASES_TITLE'); ?>:
            <input type="text" name="db_title" style="width: 550px;" />
        </label>
        <label><?php echo Lang::txt('PLG_PROJECTS_DATABASES_DESC'); ?>:
            <textarea name="db_description" style="width: 550px; height: 130px;"></textarea>
        </label>
    </form>
</div>
