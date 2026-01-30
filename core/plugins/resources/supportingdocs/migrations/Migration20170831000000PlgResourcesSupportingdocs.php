<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Supportingdocs\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Supportingdocs plugin
 **/
class Migration20170831000000PlgResourcesSupportingdocs extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'supportingdocs');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'supportingdocs');
    }
}
