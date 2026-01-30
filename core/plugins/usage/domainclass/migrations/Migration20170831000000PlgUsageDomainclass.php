<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Usage\Domainclass\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Usage - Domainclass plugin
 **/
class Migration20170831000000PlgUsageDomainclass extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('usage', 'domainclass', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('usage', 'domainclass');
    }
}
