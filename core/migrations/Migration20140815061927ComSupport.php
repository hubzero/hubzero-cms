<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'color' column to support statuses and fix column type for alias
  *
**/
class Migration20140815061927ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_statuses')) {
            if ($schema->hasColumn('#__support_statuses', 'alias')) {
                $schema->modifyColumn('#__support_statuses', 'alias')->string(250)->notNull()->default('')->execute();
            }

            if (!$schema->hasColumn('#__support_statuses', 'color')) {
                $schema->addColumn('#__support_statuses', 'color')->string(50)->notNull()->default('')->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_statuses')) {
            if ($schema->hasColumn('#__support_statuses', 'color')) {
                $schema->dropColumn('#__support_statuses', 'color');
            }

            if ($schema->hasColumn('#__support_statuses', 'alias')) {
                $schema->modifyColumn('#__support_statuses', 'alias')->string(250)->notNull()->default('')->execute();
            }
        }
    }
}
