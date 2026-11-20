<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for resetting wiki page access value that Joomla auto-set
**/
class Migration20150105220027ComWiki extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_page')) {
            $this->db
                ->getQuery()
                ->update('#__wiki_page')
                ->set(['access' => 0])
                ->where('access', '=', 1)
                ->where('group_cn', 'NOT LIKE', 'pr-%')
                ->execute();
        }
    }
}
