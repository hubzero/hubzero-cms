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
 * Migration script for Shibboleth session data that needs to survive a logout during account linking
 *
*/
class Migration20150331131922PlgAuthenticationShibboleth extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__shibboleth_sessions')) {
            $schema->createTable('#__shibboleth_sessions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('session_key', 200)
                ->text('data')
                ->timestamp('created')->defaultExpression(Expression::currentTimestamp())
                ->primaryKey('id')
                ->uniqueIndex('session_key', 'session_key')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__shibboleth_sessions')) {
            $schema->dropTable('#__shibboleth_sessions');
        }
    }
}
