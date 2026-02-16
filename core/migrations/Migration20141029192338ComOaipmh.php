<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for fixing 'jos_' refernces in OAIPMH content
  *
**/
class Migration20141029192338ComOaipmh extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $query = $this->db->getQuery(true);
        $query->update('#__oaipmh_dcspecs')
            ->set(['query' => Expression::replace('query', 'jos_', '#__')])
            ->execute();

        $schema = new Builder($this->db);

        if ($this->db->tableExists('#__oaipmh_records')) {
            // Cleanup duplicates
            $subquery = $this->db->getQuery()
                ->select('id')
                ->from('#__oaipmh_records')
                ->group('record_id')
                ->group('metadata_prefix');

            $query = $this->db->getQuery()
                ->delete('#__oaipmh_records')
                ->where('id', 'NOT IN', $subquery);
            $query->execute();

            // Add unique index
            if (!$schema->hasKey('#__oaipmh_records', 'idx_record_prefix')) {
                $schema->alterTable('#__oaipmh_records')
                    ->addUniqueIndex('idx_record_prefix', ['record_id', 'metadata_prefix'])
                    ->execute();
            }
        }
    }
}
