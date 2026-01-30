<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Templates\Kameleon\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Template - Kameleon plugin
 *
**/
class Migration20170831000000TplKameleon extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addTemplateEntry('kameleon', 'kameleon', 1, 1, 1, null, 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('kameleon', 1);
    }
}
