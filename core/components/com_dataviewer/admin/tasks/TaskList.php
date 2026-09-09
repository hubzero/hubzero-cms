<?php

/**
 * Database list task for the admin Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;

class TaskList
{
    public static function execute()
    {
        \Hubzero\Facades\Toolbar::title(
            \Hubzero\Facades\Lang::txt('Database List'),
            'databases'
        );
        \Hubzero\Facades\Toolbar::preferences(
            \Hubzero\Facades\Request::getcmd('option'),
            '500'
        );

        $base = DvConfig::$conf['dir_base'];

        // Use glob() instead of shell `ls` to find databases
        $configFiles = glob($base . '/*/database.json');
        if (!is_array($configFiles)) {
            $configFiles = [];
        }
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

        foreach ($configFiles as $configFile) {
            $id = basename(dirname($configFile));
            $db = json_decode(file_get_contents($configFile), true);
            if (!$db) {
                continue;
            }

            $configLink = '/administrator/index.php?option=com_dataviewer'
                . '&task=config&db=' . urlencode($id);
            $dvLink = '/administrator/index.php?option=com_dataviewer'
                . '&task=dataview_list&db=' . urlencode($id);
            ?>
                <tr>
                    <td><?php echo ++$c; ?></td>
                    <td><?php echo htmlspecialchars($db['name']); ?></td>
                    <td><a href="<?php echo $configLink; ?>">Edit Config</a></td>
                    <td><a href="<?php echo $dvLink; ?>" target="_blank">Dataviews</a></td>
                </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        <?php
    }
}
