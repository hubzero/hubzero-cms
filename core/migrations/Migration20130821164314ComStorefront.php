<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding storefront component entry
**/
class Migration20130821164314ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('Storefront');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('Storefront');
    }
}
