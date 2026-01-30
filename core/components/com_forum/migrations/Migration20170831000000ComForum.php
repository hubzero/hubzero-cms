<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_forum
 **/
class Migration20170831000000ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('forum');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('forum');
    }
}
