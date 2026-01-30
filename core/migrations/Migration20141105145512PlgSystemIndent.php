<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing the system plugin called 'indent'
**/
class Migration20141105145512PlgSystemIndent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('system', 'indent');
    }

    /**
     * Up
     **/
    public function down()
    {
        $this->addPluginEntry('system', 'indent');
    }
}
