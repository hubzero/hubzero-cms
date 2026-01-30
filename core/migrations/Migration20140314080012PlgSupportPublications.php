<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for getting rid of duplicate section date entries
**/
class Migration20140314080012PlgSupportPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'publications');
    }

    /**
     * Up
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'publications');
    }
}
