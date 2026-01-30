<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Oaipmh\Resources\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Oaipmh - Resources plugin
 *
*/
class Migration20170831000000PlgOaipmhResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('oaipmh', 'resources');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('oaipmh', 'resources');
    }
}
