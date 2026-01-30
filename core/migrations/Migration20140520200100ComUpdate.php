<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding update component entry
**/
class Migration20140520200100ComUpdate extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('Update', 'com_update', 1, '', false);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('Update');
    }
}
