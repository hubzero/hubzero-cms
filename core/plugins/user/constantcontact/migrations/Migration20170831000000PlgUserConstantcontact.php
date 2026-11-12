<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Constantcontact\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Constantcontact plugin
 **/
class Migration20170831000000PlgUserConstantcontact extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'constantcontact');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'constantcontact');
    }
}
