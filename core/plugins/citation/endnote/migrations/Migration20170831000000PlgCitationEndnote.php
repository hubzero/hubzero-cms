<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Citation\Endnote\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Citation - Endnote plugin
 **/
class Migration20170831000000PlgCitationEndnote extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('citation', 'endnote');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('citation', 'endnote');
    }
}
