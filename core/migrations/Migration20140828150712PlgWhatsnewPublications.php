<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling whatsnew publications plugin
 *
*/
class Migration20140828150712PlgWhatsnewPublications extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('whatsnew', 'publications');
    }

    /**
     * Up
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('whatsnew', 'publications');
    }
}
