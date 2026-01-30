<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unused com_admin component
 *
*/
class Migration20170313151515ComAdmin extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deleteComponentEntry('admin');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addComponentEntry('admin');
    }
}
