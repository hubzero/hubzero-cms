<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing several blog field data types
 *
*/
class Migration20141009203743ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db->schema()->modifyColumn('#__blog_comments', 'anonymous')
            ->tinyInteger(2)
            ->unsigned()
            ->notNull()
            ->default(0)
            ->execute();
        $this->db->schema()->modifyColumn('#__blog_entries', 'created')
            ->datetime()
            ->notNull()
            ->default('0000-00-00 00:00:00')
            ->execute();
    }
}
