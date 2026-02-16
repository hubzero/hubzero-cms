<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing oaipmh component
**/
class Migration20130813195602ComOaipmh extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__oaipmh_dcspecs')) {
            $schema->createTable('#__oaipmh_dcspecs')
                ->id()
                ->string('name', 255)
                ->text('query')
                ->integer('display')->default(0)
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $q1 = <<<SQL
SELECT p.id FROM #__publications p, #__publication_versions pv 
WHERE p.id = pv.publication_id AND pv.state = 1
SQL;
            $q3 = <<<SQL
SELECT pv.title FROM #__publication_versions pv, #__publications p 
WHERE p.id = pv.publication_id AND p.id = \$id LIMIT 1
SQL;
            $q4 = <<<SQL
SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, #__publications p 
WHERE pa.publication_version_id = pv.id 
AND pv.publication_id = p.id AND p.id = \$id LIMIT 1
SQL;
            $q5 = <<<SQL
SELECT t.raw_tag FROM #__tags t, #__tags_object tos 
WHERE t.id = tos.tagid AND tos.objectid = \$id ORDER BY t.raw_tag
SQL;
            $q6 = <<<SQL
SELECT pv.submitted FROM #__publication_versions pv, #__publications p 
WHERE p.id = pv.publication_id AND p.id = \$id ORDER BY pv.submitted LIMIT 1
SQL;
            $q7 = <<<SQL
SELECT pv.doi FROM #__publication_versions pv, #__publications p 
WHERE p.id = pv.publication_id AND pv.state = 1 AND p.id = \$id
SQL;
            $q8 = <<<SQL
SELECT pv.description FROM #__publication_versions pv, #__publications p 
WHERE p.id = pv.publication_id AND p.id = \$id LIMIT 1
SQL;
            $q11 = <<<SQL
SELECT pl.title FROM #__publications p, #__publication_versions pv, #__publication_licenses pl 
WHERE pl.id = pv.license_type 
AND pv.publication_id = p.id AND p.id = \$id LIMIT 1
SQL;
            $q12 = <<<SQL
SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, #__publications p 
WHERE pa.publication_version_id = pv.id 
AND pv.publication_id = p.id AND p.id = \$id AND pv.state = 1
SQL;
            $q13 = <<<SQL
SELECT DISTINCT path FROM #__publication_attachments pa 
WHERE publication_id = \$id AND role = 1 ORDER BY path
SQL;

            $this->db->getQuery(true)
                ->insert('#__oaipmh_dcspecs')
                ->columns(['id', 'name', 'query', 'display'])
                ->values([
                    [1, 'resource IDs', $q1, 1],
                    [2, 'specify sets', '', 1],
                    [3, 'title', $q3, 1],
                    [4, 'creator', $q4, 1],
                    [5, 'subject', $q5, 1],
                    [6, 'date', $q6, 1],
                    [7, 'identifier', $q7, 1],
                    [8, 'description', $q8, 1],
                    [9, 'type', 'Dataset', 1],
                    [10, 'publisher', 'myhub', 1],
                    [11, 'rights', $q11, 1],
                    [12, 'contributor', $q12, 1],
                    [13, 'relation', $q13, 1],
                    [14, 'format', '', 1],
                    [15, 'coverage', '', 1],
                    [16, 'language', '', 1],
                    [17, 'source', '', 1]
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__oaipmh_dcspecs')) {
            $schema->dropTable('#__oaipmh_dcspecs');
        }
    }
}
