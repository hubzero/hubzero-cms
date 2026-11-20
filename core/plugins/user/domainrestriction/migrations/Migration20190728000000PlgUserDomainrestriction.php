<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'domainrestriction', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'domainrestriction');
    }
}
