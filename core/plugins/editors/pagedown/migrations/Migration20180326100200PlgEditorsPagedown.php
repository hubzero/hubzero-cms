<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * Migration script for adding Editors - Pagedown plugin
 **/
class Migration20180326100200PlgEditorsPagedown extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'pagedown');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'pagedown');
    }
}
