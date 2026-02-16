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
 * Migration script for tracking when linked accounts are created
  *
**/
class Migration20130403000000Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__auth_link', 'linked_on')) {
            $schema->addColumn('#__auth_link', 'linked_on')
                ->timestamp()
                ->notNull()
                ->defaultExpression(Expression::currentTimestamp())
                ->onUpdateExpression(Expression::currentTimestamp());
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__auth_link', 'linked_on')) {
            $schema->dropColumn('#__auth_link', 'linked_on');
        }
    }
}
