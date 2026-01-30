<?php

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Migrations;

include_once \Component::path('com_config') . DS . 'models' . DS . 'extension.php';

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_redirect
 * Add to menu this time
 **/
class Migration20250417000000Redirect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('redirect');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('redirect');
    }
}
