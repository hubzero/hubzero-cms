<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Activity component entry
 *
*/
class Migration20170329190610ComActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('activity');

        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set(['protected' => 1])
            ->where('element', '=', 'com_activity')
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('activity');
    }
}
