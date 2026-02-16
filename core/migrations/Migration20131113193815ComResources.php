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
 * Migration script for replacing odd characters in resource license text
  *
**/
class Migration20131113193815ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_licenses')) {
            $this->db->getQuery(true)
                ->update('#__resource_licenses')
                ->set(['text' => Expression::replace('text', 'â€”', '—')])
                ->execute();
        }
    }
}
