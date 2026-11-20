<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Middleware\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Middleware plugin
 **/
class Migration20170831000000PlgUserMiddleware extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'middleware');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'middleware');
    }
}
