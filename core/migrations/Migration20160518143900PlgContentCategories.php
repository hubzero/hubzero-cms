<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming Joomla content plugin
 *
*/
class Migration20160518143900PlgContentCategories extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'name'    => 'plg_content_categories',
                    'element' => 'categories'
                ])
                ->where('folder', '=', 'content')
                ->where('element', '=', 'joomla')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'name'    => 'plg_content_joomla',
                    'element' => 'joomla'
                ])
                ->where('folder', '=', 'content')
                ->where('element', '=', 'categories')
                ->execute();
        }
    }
}
