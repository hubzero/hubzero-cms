<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'status_code' field to redirect_links table
 *
*/
class Migration20170921161800ComRedirect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__redirect_links')) {
            if (!$schema->hasColumn('#__redirect_links', 'status_code')) {
                $schema->addColumn('#__redirect_links', 'status_code')->integer(3)->notNull()->default('404');

                $this->db->getQuery(true)
                    ->update('#__redirect_links')
                    ->set(['status_code' => 301])
                    ->where('new_url', '!=', '')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__redirect_links')) {
            if ($schema->hasColumn('#__redirect_links', 'status_code')) {
                $schema->dropColumn('#__redirect_links', 'status_code');
            }
        }
    }
}
