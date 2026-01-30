<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Xmessage\Handler\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Xmessage - Handler plugin
 **/
class Migration20170831000000PlgXmessageHandler extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('xmessage', 'handler');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('xmessage', 'handler');
    }
}
