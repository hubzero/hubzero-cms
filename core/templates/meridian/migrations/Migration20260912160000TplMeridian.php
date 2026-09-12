<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Templates\Meridian\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Template
 *
**/
class Migration20260912160000TplMeridian extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addTemplateEntry('meridian', 'Meridian, a hub before it has a subject', 0, 1, 0, null, 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('meridian', 0);
    }
}
