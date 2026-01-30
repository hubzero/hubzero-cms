<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for for adding time plugin for support
 *
*/
class Migration20140505160720PlgSupportTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'time', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'time');
    }
}
