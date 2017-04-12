<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ERC extension to citations, determines which buttons
 * are active to redirect to their visualization applications from the
 * /citations/curate controller
 **/
class Migration20170412163541ComCitations extends Base
{
        /**
         * Up
         **/
        public function up()
        {
                foreach ([
                'CREATE TABLE `jos_citations_visualizations` (
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `name` varchar(50) NOT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `id` (`id`)
                ) ENGINE=InnoDB',
                'INSERT INTO #__citations_visualizations(name) VALUES (\'CoAuthorship\')'
                ] as $sql) {
                        $this->db->setQuery($sql);
                        $this->db->query();
                }

        }

        /**
         * Down
         **/
        public function down()
        {

        }
}
