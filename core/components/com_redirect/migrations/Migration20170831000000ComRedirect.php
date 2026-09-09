<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_redirect
 **/
class Migration20170831000000ComRedirect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Create component entry but do NOT create a menu item as
        // com_redirect was originally separate from the 'components' list.
        // See Migration20250417000000Redirect for the menu item addition.
        $this->addComponentEntry('redirect', null, 1, '', false);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('redirect');
    }
}
