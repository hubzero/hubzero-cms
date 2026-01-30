<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cart\Paypal\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cart - Paypal plugin
 **/
class Migration20170831000000PlgCartPaypal extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cart', 'paypal', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cart', 'paypal');
    }
}
