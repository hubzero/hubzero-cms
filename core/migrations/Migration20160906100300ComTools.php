<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to fix index naming conventions in middleware tables
 *
*/
class Migration20160906100300ComTools extends Base
{
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if (
            $mwdb->schema()->tableExists('view')
            && $mwSchema->hasColumn('view', 'viewid')
            && !$mwSchema->hasPrimaryKey('view')
        ) {
            $mwSchema->addPrimaryKey('view', 'viewid');
        }

        if (
            $mwdb->schema()->tableExists('view')
            && $mwSchema->hasColumn('view', 'viewid')
            && $mwSchema->hasKey('view', 'viewid')
        ) {
            $mwdb->schema()->dropIndex('view', 'viewid');
        }

        if (
            $mwdb->schema()->tableExists('sessionpriv')
            && $mwSchema->hasColumn('sessionpriv', 'privid')
            && !$mwSchema->hasPrimaryKey('sessionpriv')
        ) {
            $mwSchema->addPrimaryKey('sessionpriv', 'privid');
        }

        if (
            $mwdb->schema()->tableExists('sessionpriv')
            && $mwSchema->hasColumn('sessionpriv', 'privid')
            && $mwSchema->hasKey('sessionpriv', 'privid')
        ) {
            $mwdb->schema()->dropIndex('sessionpriv', 'privid');
        }

        if (
            $mwdb->schema()->tableExists('session')
            && $mwSchema->hasColumn('session', 'sessnum')
            && !$mwSchema->hasPrimaryKey('session')
        ) {
            $mwSchema->addPrimaryKey('session', 'sessnum');
        }

        if (
            $mwdb->schema()->tableExists('session')
            && $mwSchema->hasColumn('session', 'sessnum')
            && $mwSchema->hasKey('session', 'sessnum')
        ) {
            $mwdb->schema()->dropIndex('session', 'sessnum');
        }

        $mwdb->schema()->addIndex('joblog', 'idx_sessnum', 'sessnum');

        if (
            $mwdb->schema()->tableExists('joblog')
            && $mwSchema->hasColumn('joblog', 'sessnum')
            && $mwSchema->hasKey('joblog', 'sessnum')
        ) {
            $mwdb->schema()->dropIndex('joblog', 'sessnum');
        }

        $mwdb->schema()->addIndex('joblog', 'idx_event', 'event');

        if (
            $mwdb->schema()->tableExists('joblog')
            && $mwSchema->hasColumn('joblog', 'event')
            && $mwSchema->hasKey('joblog', 'event')
        ) {
            $mwdb->schema()->dropIndex('joblog', 'event');
        }

        $mwdb->schema()->addUniqueIndex('job', 'uidx_jobid', 'jobid');

        $mwdb->schema()->dropIndex('job', 'jobid');

        $mwdb->schema()->dropIndex('job', 'start');

        $mwdb->schema()->dropIndex('job', 'start_2');

        $mwdb->schema()->dropIndex('job', 'heartbeat_2');

        $mwdb->schema()->dropIndex('job', 'heartbeat');

        $mwdb->schema()->addIndex('domainclass', 'idx_class', 'class');

        $mwdb->schema()->dropIndex('domainclass', 'class');

        if (
            $mwdb->schema()->tableExists('domainclass')
            && $mwSchema->hasColumn('domainclass', 'class')
        ) {
            $mwdb->schema()->addIndex('domainclass', 'idx_domain_class', ['domain', 'class']);
        }

        $mwdb->schema()->dropIndex('domainclass', 'domain');

        $mwdb->schema()->dropIndex('display', 'hostname');
    }

    public function down()
    {
    }
}
