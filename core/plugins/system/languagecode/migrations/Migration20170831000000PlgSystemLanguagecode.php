<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Languagecode\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Languagecode plugin
 **/
class Migration20170831000000PlgSystemLanguagecode extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'languagecode', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'languagecode');
    }
}
