<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making com_redirect visible in admin menu
 **/
class Migration20250417000000Redirect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('redirect');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('redirect');
    }
}
