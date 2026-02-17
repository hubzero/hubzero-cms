<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding fields to the feedback table and
 * dropping redundant selected_quotes table
**/
class Migration20140609160011ComFeedback extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__feedback')) {
            if (!$schema->hasColumn('#__feedback', 'miniquote')) {
                $schema->addColumn('#__feedback', 'miniquote')
                    ->string(255)
                    ->notNull()
                    ->default('')
                    ->execute();
            }

            if (!$schema->hasColumn('#__feedback', 'admin_rating')) {
                $schema->addColumn('#__feedback', 'admin_rating')
                    ->tinyInteger()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__feedback', 'notable_quote')) {
                $schema->addColumn('#__feedback', 'notable_quote')
                    ->tinyInteger()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__feedback', 'user_id')) {
                $schema->renameColumn('#__feedback', 'userid', 'user_id')
                    ->integer()
                    ->nullable()
                    ->execute();
            }
        }

        if ($schema->tableExists('#__selected_quotes')) {
            $results = $this->db->getQuery(true)
                ->select('sq.*')
                ->select('f.id', 'fid')
                ->from('#__selected_quotes', 'sq')
                ->joinOn('#__feedback AS f', [
                    ['f.quote', '=', 'sq.quote'],
                    ['f.user_id', '=', 'sq.userid'],
                ], 'left')
                ->loadObjectList();

            if ($results) {
                foreach ($results as $result) {
                    if ($result->fid) {
                        // Update existing feedback entry
                        $this->db->getQuery(true)
                            ->update('#__feedback')
                            ->set([
                                'user_id'       => $result->userid,
                                'fullname'      => $result->fullname,
                                'org'           => $result->org,
                                'quote'         => $result->quote,
                                'notes'         => $result->notes,
                                'picture'       => $result->picture,
                                'publish_ok'    => 1,
                                'date'          => $result->date,
                                'miniquote'     => $result->miniquote,
                                'notable_quote' => $result->notable_quotes,
                            ])
                            ->where('id', '=', $result->fid)
                            ->execute();
                    } else {
                        // Insert new feedback entry
                        $this->db->getQuery(true)
                            ->insert('#__feedback')
                            ->set([
                                'user_id'       => $result->userid,
                                'fullname'      => $result->fullname,
                                'org'           => $result->org,
                                'quote'         => $result->quote,
                                'notes'         => $result->notes,
                                'picture'       => $result->picture,
                                'publish_ok'    => 1,
                                'date'          => $result->date,
                                'miniquote'     => $result->miniquote,
                                'notable_quote' => $result->notable_quotes,
                            ])
                            ->execute();
                    }
                }
            }

            $schema->dropTable('#__selected_quotes');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__selected_quotes')) {
            $schema->createTable('#__selected_quotes')
                ->integer('id', ['autoIncrement' => true])
                ->integer('userid')->default(0)
                ->string('fullname', 100)->default('')
                ->string('org', 200)->default('')
                ->string('miniquote', 200)->default('')
                ->text('short_quote')->nullable()
                ->text('quote')->nullable()
                ->string('picture', 250)->default('')
                ->datetime('date')->default('0000-00-00 00:00:00')
                ->tinyInteger('flash_rotation')->default(0)
                ->tinyInteger('notable_quotes')->default(1)
                ->text('notes')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if ($schema->tableExists('#__feedback')) {
            if ($schema->hasColumn('#__feedback', 'miniquote')) {
                $schema->dropColumn('#__feedback', 'miniquote');
            }

            if ($schema->hasColumn('#__feedback', 'admin_rating')) {
                $schema->dropColumn('#__feedback', 'admin_rating');
            }

            if ($schema->hasColumn('#__feedback', 'notable_quote')) {
                $schema->dropColumn('#__feedback', 'notable_quote');
            }

            if ($schema->hasColumn('#__feedback', 'user_id')) {
                $schema->renameColumn('#__feedback', 'user_id', 'userid')
                    ->integer()
                    ->nullable()
                    ->execute();
            }
        }
    }
}
