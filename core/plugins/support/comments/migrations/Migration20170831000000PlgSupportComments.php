<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Comments\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Comments plugin
 **/
class Migration20170831000000PlgSupportComments extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'comments');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'comments');
    }
}
