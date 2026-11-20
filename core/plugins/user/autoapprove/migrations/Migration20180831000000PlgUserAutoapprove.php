<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Autoapprove\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Auto-approve plugin
 **/
class Migration20180831000000PlgUserAutoapprove extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'autoapprove', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'autoapprove');
    }
}
