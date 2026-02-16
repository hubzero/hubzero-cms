<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the collect module and removing collect plugins
 *
 */
class Migration20141009072712ModCollect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('content', 'collect');
        $this->deletePluginEntry('resources', 'collect');
        $this->deletePluginEntry('wiki', 'collect');

        $this->addModuleEntry('mod_collect', 1, '', 0);

        $count = $this->db->getQuery(true)
            ->from('#__modules')
            ->where('module', '=', 'mod_collect')
            ->count();

        if (!$count) {
            $position = 'endpage';
            $found = false;

            $count = $this->db->getQuery(true)
                ->from('#__modules')
                ->where('client_id', '=', 0)
                ->where('position', '=', $position)
                ->count();

            if ($count) {
                $found = true;
            }

            if (!$found) {
                $position = 'footer';

                $count = $this->db->getQuery(true)
                    ->from('#__modules')
                    ->where('client_id', '=', 0)
                    ->where('position', '=', $position)
                    ->count();

                if ($count) {
                    $found = true;
                }
            }

            if ($found) {
                $this->installModule('collect', $position);
            }
        }
    }

    /**
     * Up
     **/
    public function down()
    {
        $this->addPluginEntry('content', 'collect');
        $this->addPluginEntry('resources', 'collect');
        $this->addPluginEntry('wiki', 'collect');

        $this->deleteModuleEntry('mod_collect');
    }
}
