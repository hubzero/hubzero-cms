<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a Watch plugin for publications
**/
class Migration20150518180000PlgPublicationsWatch extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'watch', 0);
    }

    /**
     * Up
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'watch');
    }
}
