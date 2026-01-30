<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Shibboleth\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Shibboleth plugin
 **/
class Migration20170831000000PlgAuthenticationShibboleth extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'shibboleth', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'shibboleth');
    }
}
