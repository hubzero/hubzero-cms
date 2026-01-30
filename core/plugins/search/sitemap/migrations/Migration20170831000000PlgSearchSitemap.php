<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Sitemap\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Sitemap plugin
 **/
class Migration20170831000000PlgSearchSitemap extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'sitemap');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'sitemap');
    }
}
