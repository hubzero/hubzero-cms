<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Templates\Lucent\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Template
 *
**/
class Migration20191016000001TplLucent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addTemplateEntry('lucent', 'Lucent, the template that inspires', 0, 1, 0, null, 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('lucent', 0);
    }
}
