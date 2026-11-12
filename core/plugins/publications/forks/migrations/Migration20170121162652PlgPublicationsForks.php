<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Forks\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Publications - Forks plugin
 **/
class Migration20170121162652PlgPublicationsForks extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'forks');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'forks');
    }
}
