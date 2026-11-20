<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'csp', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'csp');
    }
}
