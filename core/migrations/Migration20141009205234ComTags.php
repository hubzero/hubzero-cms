<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing up tags tag field data type
 *
*/
class Migration20141009205234ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db->schema()->modifyColumn('#__tags', 'tag')
            ->string(100)
            ->notNull()
            ->default('')
            ->execute();
    }
}
