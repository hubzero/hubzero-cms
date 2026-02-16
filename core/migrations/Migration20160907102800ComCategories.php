<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add default value to field for consistency between upgrade/new installs
 *
*/
class Migration20160907102800ComCategories extends Base
{
    public function up()
    {
        $this->db->schema()->modifyColumn('#__categories', 'title')
            ->string(255)
            ->notNull()
            ->default('')
            ->execute();
    }

    public function down()
    {
    }
}
