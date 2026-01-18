<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_collections
 **/
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class Migration20170831000000ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('collections');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('collections');
    }
}
