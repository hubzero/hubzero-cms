<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for 2011/12 middleware table modifications
  *
**/
class Migration20120101000002Core extends Base
{
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if (
            $mwSchema->tableExists('display')
            && $mwSchema->hasColumn('display', 'hostname')
        ) {
            $mwSchema->addIndex('display', 'idx_hostname', 'hostname');
        }

        if (
            $mwSchema->tableExists('host')
            && $mwSchema->hasColumn('host', 'hostname')
            && !$mwSchema->hasPrimaryKey('host')
        ) {
            $mwSchema->table('host')->alter()
                ->addPrimaryKey('hostname')
                ->execute();
        }

        if ($mwSchema->tableExists('hosttype') && $mwSchema->hasPrimaryKey('hosttype')) {
            $mwSchema->dropPrimaryKey('hosttype');
        }

        if ($mwSchema->tableExists('job')) {
            $mwSchema->addIndex('job', 'idx_start', 'start');
            $mwSchema->addIndex('job', 'idx_heartbeat', 'heartbeat');
        }

        if ($mwSchema->tableExists('joblog')) {
            if ($mwSchema->hasColumn('joblog', 'walltime')) {
                $mwSchema->modifyColumn('joblog', 'walltime')
                    ->double()
                    ->unsigned()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if ($mwSchema->hasColumn('joblog', 'cputime')) {
                $mwSchema->modifyColumn('joblog', 'cputime')
                    ->double()
                    ->unsigned()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if ($mwSchema->hasPrimaryKey('joblog')) {
                $mwSchema->table('joblog')->alter()
                    ->dropPrimaryKey()
                    ->addPrimaryKey(['sessnum', 'job', 'event', 'venue'])
                    ->execute();
            } else {
                $mwSchema->table('joblog')->alter()
                    ->addPrimaryKey(['sessnum', 'job', 'event', 'venue'])
                    ->execute();
            }
        }

        if ($mwSchema->tableExists('session') && $mwSchema->hasColumn('session', 'sessname')) {
            $mwSchema->modifyColumn('session', 'sessname')
                ->string(100)
                ->notNull()
                ->default('')
                ->execute();
        }

        if ($mwSchema->tableExists('sessionlog')) {
            if ($mwSchema->hasColumn('sessionlog', 'sessnum')) {
                $mwSchema->modifyColumn('sessionlog', 'sessnum')
                    ->bigInteger()
                    ->unsigned()
                    ->notNull()
                    ->autoIncrement()
                    ->execute();
            }

            if ($mwSchema->hasColumn('sessionlog', 'walltime')) {
                $mwSchema->modifyColumn('sessionlog', 'walltime')
                    ->double()
                    ->unsigned()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if ($mwSchema->hasColumn('sessionlog', 'viewtime')) {
                $mwSchema->modifyColumn('sessionlog', 'viewtime')
                    ->double()
                    ->unsigned()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if ($mwSchema->hasColumn('sessionlog', 'cputime')) {
                $mwSchema->modifyColumn('sessionlog', 'cputime')
                    ->double()
                    ->unsigned()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }
        }

        if ($mwSchema->tableExists('view') && $mwSchema->hasColumn('view', 'referrer')) {
            $mwSchema->dropColumn('view', 'referrer');
        }
    }
}
