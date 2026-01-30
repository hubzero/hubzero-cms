<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for cleaning up some old component entries
  *
**/
class Migration20140428183704Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deleteComponentEntry('apc');
        $this->deleteComponentEntry('geodb');
        $this->deleteComponentEntry('ldap');
        $this->deleteComponentEntry('myhub');
        $this->deleteComponentEntry('xflash');
        $this->deleteComponentEntry('xpoll');
    }
}
