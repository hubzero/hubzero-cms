<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Facades\Date;

/**
 * Migration script for adding curation version table and fill with available data
**/
class Migration20150520110000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Create versions table
        if (!$schema->tableExists('#__publication_curation_versions')) {
            $schema->createTable('#__publication_curation_versions')
                ->integer('id', ['autoIncrement' => true])
                ->integer('type_id')->default(0)
                ->integer('version_number')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->text('curation')
                ->primaryKey('id')
                ->index('idx_type_id_version_number', ['type_id', 'version_number'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Add column to versions table and populate with historic data
        if ($schema->tableExists('#__publication_versions')) {
            // Add curation_version_id column
            if (!$schema->hasColumn('#__publication_versions', 'curation_version_id')) {
                $schema->addColumn('#__publication_versions', 'curation_version_id')->integer(11);
            }

            // Get versions with saved curation
            if (
                $schema->hasColumn('#__publication_versions', 'curation')
                && $schema->hasColumn('#__publication_master_types', 'curation')
            ) {
                $query = $this->db->getQuery(true)
                    ->select('v.curation')
                    ->distinct()
                    ->select('t.id', 'type_id')
                    ->select('t.curation', 'master_curation')
                    ->from('#__publication_versions', 'v')
                    ->innerJoin('#__publications AS p', 'p.id', 'v.publication_id')
                    ->innerJoin('#__publication_master_types AS t', 't.id', 'p.master_type')
                    ->whereIsNotNull('v.curation')
                    ->where('v.curation', '!=', '')
                    ->whereIsNotNull('v.accepted')
                    ->where('v.accepted', '!=', $this->db->getNullDate())
                    ->order('v.accepted', 'ASC');

                $results = $query->loadObjectList();

                if ($results && count($results) > 0) {
                    foreach ($results as $result) {
                        // Determine version number
                        $query = $this->db->getQuery(true)
                            ->select('MAX(version_number)')
                            ->from('#__publication_curation_versions')
                            ->where('type_id', '=', $result->type_id);
                        $versionNumber = $query->value('MAX(version_number)');
                        $versionNumber = intval($versionNumber) + 1;

                        $stq = new \Components\Publications\Tables\CurationVersion($this->db);
                        $stq->type_id         = $result->type_id;
                        $stq->created         = Date::toSql();
                        $stq->version_number  = $versionNumber;
                        $stq->curation        = $result->curation;

                        if ($stq->store()) {
                            $this->db->getQuery(true)
                                ->update('#__publication_versions')
                                ->set(['curation_version_id' => $stq->id])
                                ->where('curation', '=', $result->curation)
                                ->execute();
                        }
                    }
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Drop versions table
        $schema->dropTable('#__publication_curation_versions');

        if ($schema->tableExists('#__publication_versions')) {
            // Drop curation_version_id column
            if ($schema->hasColumn('#__publication_versions', 'curation_version_id')) {
                $schema->dropColumn('#__publication_versions', 'curation_version_id');
            }
        }
    }
}
