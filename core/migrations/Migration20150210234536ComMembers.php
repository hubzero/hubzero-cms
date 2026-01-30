<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for disabling new user admin notifications by default
 *
*/
class Migration20150210234536ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $params = $this->getParams('com_users');
        $params->set('mail_to_admin', '0');

        $this->saveParams('com_users', $params);
    }
}
