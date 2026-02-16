<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding missing fields to citations
 *
*/
class Migration20121027000000ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations')) {
            // MODIFY with AFTER clause is MySQL-specific; column position change skipped for SQLite
            if ($schema->hasColumn('#__citations', 'type')) {
                $schema->modifyColumn('#__citations', 'type')
                    ->string(30)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }

            if (!$schema->hasColumn('#__citations', 'language')) {
                $schema->addColumn('#__citations', 'language')
                    ->string(100)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'accession_number')) {
                $schema->addColumn('#__citations', 'accession_number')
                    ->string(100)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'short_title')) {
                $schema->addColumn('#__citations', 'short_title')
                    ->string(250)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'author_address')) {
                $schema->addColumn('#__citations', 'author_address')
                    ->text()
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'keywords')) {
                $schema->addColumn('#__citations', 'keywords')
                    ->text()
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'abstract')) {
                $schema->addColumn('#__citations', 'abstract')
                    ->text()
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'call_number')) {
                $schema->addColumn('#__citations', 'call_number')
                    ->string(100)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'label')) {
                $schema->addColumn('#__citations', 'label')
                    ->string(100)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'research_notes')) {
                $schema->addColumn('#__citations', 'research_notes')
                    ->text()
                    ->execute();
            }
            if (!$schema->hasColumn('#__citations', 'params')) {
                $schema->addColumn('#__citations', 'params')
                    ->text()
                    ->execute();
            }

            // Add FULLTEXT index using helper for SQLite compatibility
            $schema->addFulltextIndex('#__citations', 'ftidx_title_isbn_doi_abstract_author_publisher', [
                'title',
                'isbn',
                'doi',
                'abstract',
                'author',
                'publisher',
            ]);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations')) {
            if ($schema->hasColumn('#__citations', 'language')) {
                $schema->dropColumn('#__citations', 'language');
            }
            if ($schema->hasColumn('#__citations', 'accession_number')) {
                $schema->dropColumn('#__citations', 'accession_number');
            }
            if ($schema->hasColumn('#__citations', 'short_title')) {
                $schema->dropColumn('#__citations', 'short_title');
            }
            if ($schema->hasColumn('#__citations', 'author_address')) {
                $schema->dropColumn('#__citations', 'author_address');
            }
            if ($schema->hasColumn('#__citations', 'keywords')) {
                $schema->dropColumn('#__citations', 'keywords');
            }
            if ($schema->hasColumn('#__citations', 'abstract')) {
                $schema->dropColumn('#__citations', 'abstract');
            }
            if ($schema->hasColumn('#__citations', 'call_number')) {
                $schema->dropColumn('#__citations', 'call_number');
            }
            if ($schema->hasColumn('#__citations', 'label')) {
                $schema->dropColumn('#__citations', 'label');
            }
            if ($schema->hasColumn('#__citations', 'research_notes')) {
                $schema->dropColumn('#__citations', 'research_notes');
            }
            if ($schema->hasColumn('#__citations', 'params')) {
                $schema->dropColumn('#__citations', 'params');
            }
            if ($schema->hasKey('#__citations', 'ftidx_title_isbn_doi_abstract_author_publisher')) {
                $schema->dropIndex('#__citations', 'ftidx_title_isbn_doi_abstract_author_publisher');
            }
        }
    }
}
