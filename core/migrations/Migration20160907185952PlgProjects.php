<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Project Info, renaming Project blog, updating ordering
 *
*/
class Migration20160907185952PlgProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->addPluginEntry('projects', 'info');

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'feed'])
                ->where('folder', '=', 'projects')
                ->where('element', '=', 'blog')
                ->where('type', '=', 'plugin')
                ->execute();

            $plugins = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('folder', '=', 'projects')
                ->where('type', '=', 'plugin')
                ->order('ordering', 'ASC')
                ->loadObjectList();

            $i = 1;
            foreach ($plugins as $plugin) {
                // Skip number 2
                if ($i == 2) {
                    // Up it to number 3
                    $i++;
                }

                $num = $i;
                // Force info to second place
                if ($plugin->element == 'info') {
                    $num = 2;
                }

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['ordering' => (int)$num])
                    ->where('extension_id', '=', (int)$plugin->extension_id)
                    ->execute();

                $i++;
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->deletePluginEntry('projects', 'info');

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'blog'])
                ->where('folder', '=', 'projects')
                ->where('element', '=', 'feed')
                ->where('type', '=', 'plugin')
                ->execute();
        }
    }
}
