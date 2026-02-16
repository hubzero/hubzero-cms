<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for specifying citation format
 *
*/
class Migration20130816143341ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        //create new format table
        if (!$schema->tableExists('#__citations_format')) {
            $schema->createTable('#__citations_format')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('typeid')->nullable()
                ->string('style', 50)->nullable()
                ->text('format')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // get citation params
        if ($schema->tableExists('#__extensions')) {
            $rawCitationParams = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_citations')
                ->value('params');
        } else {
            $rawCitationParams = $this->db->getQuery(true)
                ->select('params')
                ->from('#__components')
                ->where('option', '=', 'com_citations')
                ->value('params');
        }

        $citationParams = new \Hubzero\Config\Registry($rawCitationParams);

        //insert default format
        $hasNullType = $this->db->getQuery(true)
            ->select('typeid')
            ->from('#__citations_format')
            ->whereIsNull('typeid')
            ->value('typeid');

        if (!$hasNullType) {
            $this->db->getQuery(true)
                ->insert('#__citations_format')
                ->set([
                    'typeid' => null,
                    'style'  => 'custom',
                    'format' => $citationParams->get('citation_format', '')
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

        if ($schema->tableExists('#__citations_format')) {
            $schema->dropTable('#__citations_format');
        }
    }
}
