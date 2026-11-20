<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Pucas\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Pucas plugin
 **/
class Migration20170831000000PlgAuthenticationPucas extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'pucas', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'pucas');
    }
}
