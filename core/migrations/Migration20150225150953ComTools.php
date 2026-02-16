<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing signed nature of uses and max_uses fields
  *
**/
class Migration20150225150953ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        // Note: Middleware database is typically MySQL-only
        // These MODIFY COLUMN operations are kept as-is for middleware
        $info = $mwdb->schema()->getTableColumns('host', false);

        if (
            $mwdb->schema()->tableExists('host')
            && $mwdb->schema()->hasColumn('host', 'max_uses')
            && $info['max_uses']->Type == 'int(11) unsigned'
        ) {
            $mwdb->schema()->modifyColumn('host', 'max_uses')->integer()->notNull()->default(0)->execute();
        }

        if (
            $mwdb->schema()->tableExists('host')
            && $mwdb->schema()->hasColumn('host', 'uses')
            && $info['uses']->Type == 'int(11) unsigned'
        ) {
            $mwdb->schema()->modifyColumn('host', 'uses')->integer()->notNull()->default(0)->execute();
        }
    }
}
