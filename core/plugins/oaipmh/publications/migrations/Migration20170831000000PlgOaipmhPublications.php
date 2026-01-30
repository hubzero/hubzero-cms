<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Oaipmh\Publications\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Oaipmh - Publications plugin
 *
*/
class Migration20170831000000PlgOaipmhPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('oaipmh', 'publications');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('oaipmh', 'publications');
    }
}
