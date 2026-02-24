<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for fixing tag created and created_by info from tag logs
**/
class Migration20141222135427ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags') && $schema->tableExists('#__tags_log')) {
            $this->db->getQuery(true)
                ->update('#__tags', 't')
                ->innerJoin('#__tags_log AS l', 't.id', 'l.tag_id')
                ->set(['t.created' => Expression::raw('l.timestamp'), 't.created_by' => Expression::raw('l.user_id')])
                ->where('t.created_by', '=', 0)
                ->where('l.action', '=', 'tag_created')
                ->execute();
        }
    }
}
