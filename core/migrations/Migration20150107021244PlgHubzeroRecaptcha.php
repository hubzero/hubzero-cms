<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for clearing old recapta keys
  *
**/
class Migration20150107021244PlgHubzeroRecaptcha extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // delete and then add to clear old keys
        $this->deletePluginEntry('hubzero', 'recaptcha');
        $this->addPluginEntry('hubzero', 'recaptcha');
    }
}
