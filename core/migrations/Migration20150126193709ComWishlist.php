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
 * Migration script for fixing old wishlist names
 *
*/
class Migration20150126193709ComWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wishlist')) {
            $query = $this->db->getQuery(true);
            $query->update('#__wishlist')
                ->set(['title' => Expression::replace('title', 'WISHLIST_NAME_GROUP', 'Group')])
                ->execute();
        }
    }
}
