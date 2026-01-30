<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Formathtml plugin
 *
**/
class Migration20170831000000PlgContentFormathtml extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'formathtml');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'formathtml');
    }
}
