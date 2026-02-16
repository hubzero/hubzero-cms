<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to fix character encoding on field (from past schema upgrade)
 *
*/
class Migration20160907103500ComBillboards extends Base
{
    public function up()
    {
        $this->db->schema()->modifyColumn('#__billboards_collections', 'name')
            ->string(255)
            ->nullable()
            ->default(null)
            ->execute();
    }

    public function down()
    {
    }
}
