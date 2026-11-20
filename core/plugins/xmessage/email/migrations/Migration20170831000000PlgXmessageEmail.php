<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Xmessage\Email\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Xmessage - Email plugin
 **/
class Migration20170831000000PlgXmessageEmail extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('xmessage', 'email');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('xmessage', 'email');
    }
}
