<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Event\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Newsletter - Event plugin
 **/
class Migration20170831000000PlgNewsletterEvent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('newsletter', 'event');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('newsletter', 'event');
    }
}
