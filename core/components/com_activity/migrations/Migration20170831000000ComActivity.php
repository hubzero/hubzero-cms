<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Activity\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_activity
 **/
class Migration20170831000000ComActivity extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addComponentEntry('activity');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteComponentEntry('activity');
    }
}
