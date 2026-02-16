<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for normalizing citations tables
**/
class Migration20140818160255ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // lang field
        if (!$schema->hasColumn('#__citations', 'language')) {
            $schema->addColumn('#__citations', 'language')
                ->string(100)
                ->execute();
        }

        // accession number field
        if (!$schema->hasColumn('#__citations', 'accession_number')) {
            $schema->addColumn('#__citations', 'accession_number')
                ->string(100)
                ->execute();
        }

        // short title field
        if (!$schema->hasColumn('#__citations', 'short_title')) {
            $schema->addColumn('#__citations', 'short_title')
                ->string(250)
                ->execute();
        }

        // author address
        if (!$schema->hasColumn('#__citations', 'author_address')) {
            $schema->addColumn('#__citations', 'author_address')
                ->text()
                ->execute();
        }

        // keywords
        if (!$schema->hasColumn('#__citations', 'keywords')) {
            $schema->addColumn('#__citations', 'keywords')
                ->text()
                ->execute();
        }

        // abstract
        if (!$schema->hasColumn('#__citations', 'abstract')) {
            $schema->addColumn('#__citations', 'abstract')
                ->text()
                ->execute();
        }

        // call #
        if (!$schema->hasColumn('#__citations', 'call_number')) {
            $schema->addColumn('#__citations', 'call_number')
                ->string(100)
                ->execute();
        }

        // label
        if (!$schema->hasColumn('#__citations', 'label')) {
            $schema->addColumn('#__citations', 'label')
                ->string(100)
                ->execute();
        }

        // research notes
        if (!$schema->hasColumn('#__citations', 'research_notes')) {
            $schema->addColumn('#__citations', 'research_notes')
                ->text()
                ->execute();
        }

        // params field
        if (!$schema->hasColumn('#__citations', 'params')) {
            $schema->addColumn('#__citations', 'params')
                ->text()
                ->execute();
        }

        // remove old full text index name
        $schema->dropIndex('#__citations', 'jos_citations_search_ftidx');

        // new full text index for searching
        $schema->addFulltextIndex('#__citations', 'ftidx_search', [
            'title',
            'isbn',
            'doi',
            'abstract',
            'author',
            'publisher',
        ]);
    }
}
