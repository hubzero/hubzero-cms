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
        if (!$this->db->tableExists('#__oaipmh_dcspecs')) {
            $query = "CREATE TABLE IF NOT EXISTS `#__oaipmh_dcspecs` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`name` varchar(255) NOT NULL,
							`query` text NOT NULL,
							`display` int(1) NOT NULL DEFAULT '0',
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

            $this->db->setQuery($query);
            $this->db->query();

            $q1 = 'SELECT p.id FROM #__publications p, #__publication_versions pv '
                . 'WHERE p.id = pv.publication_id AND pv.state = 1';
            $q3 = 'SELECT pv.title FROM #__publication_versions pv, #__publications p '
                . 'WHERE p.id = pv.publication_id AND p.id = \$id LIMIT 1';
            $q4 = 'SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, '
                . '#__publications p WHERE pa.publication_version_id = pv.id '
                . 'AND pv.publication_id = p.id AND p.id = \$id LIMIT 1';
            $q5 = 'SELECT t.raw_tag FROM #__tags t, #__tags_object tos '
                . 'WHERE t.id = tos.tagid AND tos.objectid = \$id ORDER BY t.raw_tag';
            $q6 = 'SELECT pv.submitted FROM #__publication_versions pv, #__publications p '
                . 'WHERE p.id = pv.publication_id AND p.id = \$id ORDER BY pv.submitted LIMIT 1';
            $q7 = 'SELECT pv.doi FROM #__publication_versions pv, #__publications p '
                . 'WHERE p.id = pv.publication_id AND pv.state = 1 AND p.id = \$id';
            $q8 = 'SELECT pv.description FROM #__publication_versions pv, #__publications p '
                . 'WHERE p.id = pv.publication_id AND p.id = \$id LIMIT 1';
            $q11 = 'SELECT pl.title FROM #__publications p, #__publication_versions pv, '
                . '#__publication_licenses pl WHERE pl.id = pv.license_type '
                . 'AND pv.publication_id = p.id AND p.id = \$id LIMIT 1';
            $q12 = 'SELECT pa.name FROM #__publication_authors pa, #__publication_versions pv, '
                . '#__publications p WHERE pa.publication_version_id = pv.id '
                . 'AND pv.publication_id = p.id AND p.id = \$id AND pv.state = 1';
            $q13 = 'SELECT DISTINCT path FROM #__publication_attachments pa '
                . 'WHERE publication_id = \$id AND role = 1 ORDER BY path';

            $query = "INSERT INTO `#__oaipmh_dcspecs` (`id`, `name`, `query`, `display`) VALUES "
                . "(1, 'resource IDs', '$q1', 1), "
                . "(2, 'specify sets', '', 1), "
                . "(3, 'title', '$q3', 1), "
                . "(4, 'creator', '$q4', 1), "
                . "(5, 'subject', '$q5', 1), "
                . "(6, 'date', '$q6', 1), "
                . "(7, 'identifier', '$q7', 1), "
                . "(8, 'description', '$q8', 1), "
                . "(9, 'type', 'Dataset', 1), "
                . "(10, 'publisher', 'myhub', 1), "
                . "(11, 'rights', '$q11', 1), "
                . "(12, 'contributor', '$q12', 1), "
                . "(13, 'relation', '$q13', 1), "
                . "(14, 'format', '', 1), "
                . "(15, 'coverage', '', 1), "
                . "(16, 'language', '', 1), "
                . "(17, 'source', '', 1);";

            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__oaipmh_dcspecs')) {
            $query = "DROP TABLE IF EXISTS `#__oaipmh_dcspecs`;";

            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
