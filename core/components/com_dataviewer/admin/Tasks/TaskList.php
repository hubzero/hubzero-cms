<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Toolbar;

class TaskList
{
    /** @return void */
    public static function execute()
    {
        Toolbar::title(Lang::txt('Database List'), 'databases');
        Toolbar::preferences(Request::getCmd('option'), '500');


        $base = DvConfig::$conf['dir_base'];
        $arry = [];
        $list = glob($base . '/*/database.json') ?: [];

        ?>
        <table class="adminlist">
            <thead>
                <tr>
                    <th width="50px">#</th>
                    <th width="40%">Name</th>
                    <th>Config</th>
                    <th width="30%">Data Views</th>
                </tr>
            </thead>
            <tbody>
        <?php
        $c = 0;

        foreach ($list as $item) {
            $id = basename(dirname($item));
            $db = json_decode((string) file_get_contents($item), true);

            print '<tr>';
            print '<td >' . ++$c . '</td>';
            print '<td >' . $db['name'] . '</td>';
            $configLink = '/administrator/index.php?option=com_dataviewer&task=config&db=' . $id;
            $dvLink = '/administrator/index.php?option=com_dataviewer&task=dataview_list&db=' . $id;
            print '<td><a href="' . $configLink . '">Edit Config</a></td>';
            print '<td><a href="' . $dvLink . '" target="_blank">Dataviews</a></td>';
            print '</tr>';
        }
        ?>
            <tbody>
        </table>
        <?php
    }
}
