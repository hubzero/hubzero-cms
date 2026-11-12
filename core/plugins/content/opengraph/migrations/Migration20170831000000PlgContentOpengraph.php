<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Opengraph\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Opengraph plugin
 *
**/
class Migration20170831000000PlgContentOpengraph extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'opengraph');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'opengraph');
    }
}
