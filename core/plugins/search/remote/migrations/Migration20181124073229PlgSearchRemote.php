<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Remote\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - SOLR plugin
 **/
class Migration20181124073229PlgSearchRemote extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'remote', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'remote');
    }
}
