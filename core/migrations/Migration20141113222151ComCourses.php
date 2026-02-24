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
 * Migration script for adding path field to course assets and subsequent updates
**/
class Migration20141113222151ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Add folder/path field
        if ($schema->tableExists('#__courses_assets') && !$schema->hasColumn('#__courses_assets', 'path')) {
            $schema->addColumn('#__courses_assets', 'path')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();

            // Set path based on asset id
            // Note: Using raw SQL as MySQL CONCAT() function is not directly supported by Query Builder
            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['path' => Expression::concat('course_id', Expression::literal('/'), 'id')])
                ->execute();
        }

        // Find all assets with >1 associations
        $assetIds = $this->db->getQuery(true)
            ->select('asset_id')
            ->select(Expression::count('asset_id'), 'count')
            ->from('#__courses_asset_associations')
            ->group('asset_id')
            ->having('count', '>', 1)
            ->loadObjectList();

        if ($assetIds && count($assetIds) > 0) {
            foreach ($assetIds as $aa) {
                $toChange = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__courses_asset_associations')
                    ->where('asset_id', '=', (int)$aa->asset_id)
                    ->order('id', 'DESC')
                    ->limit((int)($aa->count - 1))
                    ->loadObjectList();

                foreach ($toChange as $a) {
                    $oldAssetId = $a->asset_id;
                    $asset = new \Components\Courses\Models\Asset($oldAssetId);

                    if ($asset->get('id')) {
                        // Get the offering
                        $offering = 0;
                        if ($a->scope == 'asset_group') {
                            $offering = $this->db->getQuery(true)
                                ->select('offering_id')
                                ->from('#__courses_asset_groups', 'cag')
                                ->leftJoin('#__courses_units AS cu', 'cag.unit_id', 'cu.id')
                                ->where('cag.id', '=', $a->scope_id)
                                ->value('offering_id');
                        } elseif ($a->scope == 'offering') {
                            $offering = $a->scope_id;
                        }

                        $asset->copy(false);

                        $this->db->getQuery(true)
                            ->update('#__courses_asset_associations')
                            ->set(['asset_id' => (int)$asset->get('id')])
                            ->where('id', '=', $a->id)
                            ->execute();

                        if ($offering) {
                            // Update gradebook entries
                            $this->db->getQuery(true)
                                ->update('#__courses_grade_book AS g')
                                ->leftJoin('#__courses_members AS m', 'g.member_id', 'm.id')
                                ->set(['g.scope_id' => (int)$asset->get('id')])
                                ->where('g.scope_id', '=', (int)$oldAssetId)
                                ->where('g.scope', '=', 'asset')
                                ->where('m.offering_id', '=', (int)$offering)
                                ->execute();

                            // Update asset_unity
                            $this->db->getQuery(true)
                                ->update('#__courses_asset_unity AS u')
                                ->leftJoin('#__courses_members AS m', 'u.member_id', 'm.id')
                                ->set(['u.asset_id' => (int)$asset->get('id')])
                                ->where('u.asset_id', '=', (int)$oldAssetId)
                                ->where('m.offering_id', '=', (int)$offering)
                                ->execute();

                            // Update asset_views
                            $this->db->getQuery(true)
                                ->update('#__courses_asset_views AS v')
                                ->leftJoin('#__courses_members AS m', 'v.viewed_by', 'm.id')
                                ->set(['v.asset_id' => (int)$asset->get('id')])
                                ->where('v.asset_id', '=', (int)$oldAssetId)
                                ->where('m.offering_id', '=', (int)$offering)
                                ->execute();
                        }
                    } else {
                        $this->db->getQuery(true)
                            ->delete('#__courses_asset_associations')
                            ->where('id', '=', $a->id)
                            ->execute();
                    }
                }
            }
        }
    }
}
