<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Templates\Mesozoic\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Template
 *
**/
class Migration20260911180000TplMesozoic extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addTemplateEntry('mesozoic', 'Mesozoic, for a hub about deep time', 0, 1, 0, null, 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('mesozoic', 0);
    }
}
