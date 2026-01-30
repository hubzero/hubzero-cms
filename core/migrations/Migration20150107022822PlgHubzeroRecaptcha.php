<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding key to recaptcha
**/
class Migration20150107022822PlgHubzeroRecaptcha extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $params = array(
            'private' => '',
            'public'  => ''
        );

        $this->savePluginParams('hubzero', 'recaptcha', $params);
    }
}
