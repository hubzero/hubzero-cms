<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Answers\Members\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Answers - Members plugin
 *
**/
class Migration20170831000000PlgAnswersMembers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('answers', 'members');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('answers', 'members');
    }
}
