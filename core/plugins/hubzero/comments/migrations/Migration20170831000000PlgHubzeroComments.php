<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Hubzero\Comments\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Hubzero - Comments plugin
 **/
class Migration20170831000000PlgHubzeroComments extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('hubzero', 'comments');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('hubzero', 'comments');
    }
}
