<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_activity
 **/

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class Migration20170831000000ComActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('activity');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('activity');
    }
}
