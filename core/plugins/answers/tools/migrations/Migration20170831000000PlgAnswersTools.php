<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Answers\Tools\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Answers - Tools plugin
 *
**/
class Migration20170831000000PlgAnswersTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('answers', 'tools');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('answers', 'tools');
    }
}
