<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting topics component entry
**/
class Migration20140415105610ComTopics extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deleteComponentEntry('topics');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addComponentEntry('topics');
    }
}
