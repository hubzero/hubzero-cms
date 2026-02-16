<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for add watching table
  *
**/
class Migration20130521160001ComForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db->getQuery(true)
            ->update('#__forum_posts')
            ->set(['thread' => new \Hubzero\Database\Query\Expression('id')])
            ->whereIn('scope', ['site', 'group'])
            ->where('parent', '=', 0)
            ->execute();

        $this->db->getQuery(true)
            ->update('#__forum_posts')
            ->set(['thread' => new \Hubzero\Database\Query\Expression('parent')])
            ->whereIn('scope', ['site', 'group'])
            ->where('parent', '!=', 0)
            ->execute();
    }
}
