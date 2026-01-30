<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Domainrestriction\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Constantcontact plugin
 **/
class Migration20190728000000PlgUserDomainrestriction extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'domainrestriction', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'domainrestriction');
    }
}
