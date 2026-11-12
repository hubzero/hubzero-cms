<?php

/**
 * @packagehubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @licensehttp://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_redirect
 * Add to menu this time
 **/
class Migration20250417000000Redirect extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addComponentEntry('redirect');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteComponentEntry('redirect');
    }
}
