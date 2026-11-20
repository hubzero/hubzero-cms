<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $this->db
            ->schema()
            ->modifyColumn('#__categories', 'title')
            ->string(255)
            ->notNull()
            ->default('')
            ->execute();
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down() {}
}
