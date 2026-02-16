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
 * Migration script for removing document root from migration scope
**/
class Migration20140922135214Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__migrations')
            && $schema->hasColumn('#__migrations', 'scope')
        ) {
            $query = $this->db->getQuery(true);
            $query->update('#__migrations')
                ->set(['scope' => Expression::replace('scope', PATH_ROOT . DS, '')])
                ->execute();
        }
    }
}
