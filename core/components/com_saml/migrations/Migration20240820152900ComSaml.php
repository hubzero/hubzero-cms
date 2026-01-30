<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_saml
 **/
class Migration20240820152900ComSaml extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('saml');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('saml');
    }
}
