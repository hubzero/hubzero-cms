<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding publications tags plugin
 *
*/
class Migration20140421080012PlgTagsPublications extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'publications');
    }

    /**
     * Up
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'publications');
    }
}
