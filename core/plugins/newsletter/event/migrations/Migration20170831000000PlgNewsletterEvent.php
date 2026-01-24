<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

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
