<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Csp\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Content Security Policy plugin
 **/
class Migration20220222000000PlgSystemCsp extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'csp', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'csp');
    }
}
