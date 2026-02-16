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
                $path = PATH_CORE . DS . 'components' . DS . 'com_feedback' . DS . 'tables' . DS . 'quote.php';
                if (!file_exists($path)) {
                    $path = PATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_feedback'
                        . DS . 'tables' . DS . 'quotes.php';
                }
                include_once $path;

                $tbl = '\\Components\\Feedback\\Tables\\Quote';
                if (class_exists('FeedbackQuotes')) {
                    $tbl = 'FeedbackQuotes';
                }

                foreach ($results as $result) {
                    $tbl = new $tbl($this->db);
                    $tbl->id = $result->fid;
                    $tbl->user_id = $result->userid;
                    $tbl->fullname = $result->fullname;
                    $tbl->org = $result->org;
                    $tbl->quote = $result->quote;
                    $tbl->notes = $result->notes;
                    $tbl->picture = $result->picture;
                    $tbl->publish_ok = 1;
                    $tbl->date = $result->date;
                    $tbl->miniquote = $result->miniquote;
                    $tbl->notable_quote = $result->notable_quotes;
                    $tbl->store();
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
