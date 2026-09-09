<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oauth\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to remove the admin menu entry for com_oauth.
 * OAuth is a site-only component with no admin interface.
 **/
class Migration20260307000000ComOauth extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('oauth', null, 1, '', false);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addComponentEntry('oauth', null, 1, '', true);
    }
}
