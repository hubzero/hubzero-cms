<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling publications groups plugin
 *
*/
class Migration20140626100712PlgPublicationsGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'groups');
    }

    /**
     * Up
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'groups');
    }
}
