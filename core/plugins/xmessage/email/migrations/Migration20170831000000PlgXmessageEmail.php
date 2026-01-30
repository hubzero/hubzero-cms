<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $this->addPluginEntry('xmessage', 'email');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('xmessage', 'email');
    }
}
