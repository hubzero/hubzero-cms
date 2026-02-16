<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving course form dates to section dates table
 *
*/
class Migration20141112191247ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Get all of the course form deployments
        $deployments = $this->db->getQuery(true)
            ->select(['cfd.*', 'cf.asset_id', 'cu.offering_id'])
            ->from('#__courses_form_deployments', 'cfd')
            ->leftJoin('#__courses_forms AS cf', 'cfd.form_id', 'cf.id')
            ->leftJoin('#__courses_assets AS ca', 'cf.asset_id', 'ca.id')
            ->join('#__courses_asset_associations AS caa', function ($join) {
                $join->on('ca.id', '=', 'caa.asset_id')
                     ->where('caa.scope', '=', 'asset_group');
            }, 'left')
            ->leftJoin('#__courses_asset_groups AS cag', 'caa.scope_id', 'cag.id')
            ->leftJoin('#__courses_units AS cu', 'cag.unit_id', 'cu.id')
            ->loadObjectList();

        if ($deployments && count($deployments) > 0) {
            $this->callback('progress', 'init', array('Running ' . __CLASS__ . '.php:'));
            $total = count($deployments);
            $i     = 1;

            foreach ($deployments as $deployment) {
                // Get all of the sections that this deployment is in (based on offering_id from deployment query)
                $sections = $this->db->getQuery(true)
                    ->select('id')
                    ->from('#__courses_offering_sections')
                    ->where('offering_id', '=', $deployment->offering_id)
                    ->loadObjectList();

                // Now, each section must have a section date entry
                if ($sections && count($sections) > 0) {
                    foreach ($sections as $section) {
                        $found = $this->db->getQuery(true)
                            ->select('*')
                            ->from('#__courses_offering_section_dates')
                            ->where('section_id', '=', $section->id)
                            ->where('scope', '=', 'asset')
                            ->where('scope_id', '=', $deployment->asset_id)
                            ->first();

                        if (!$found) {
                            // No date exists...so add it
                            $this->db->getQuery(true)
                                ->insert('#__courses_offering_section_dates')
                                ->columns(['section_id', 'scope', 'scope_id', 'publish_up', 'publish_down', 'created'])
                                ->values([
                                    $section->id,
                                    'asset',
                                    $deployment->asset_id,
                                    $deployment->start_time,
                                    $deployment->end_time,
                                    with(new \Hubzero\Utility\Date('now'))->toSql()
                                ])
                                ->execute();
                        } else {
                            $start = (isset($found->publish_up) && $found->publish_up != '0000-00-00 00:00:00')
                                ? $found->publish_up
                                : $deployment->start_time;
                            $end = (isset($found->publish_down) && $found->publish_down != '0000-00-00 00:00:00')
                                ? $found->publish_down
                                : $deployment->end_time;

                            $this->db->getQuery(true)
                                ->update('#__courses_offering_section_dates')
                                ->set([
                                    'publish_up' => $start,
                                    'publish_down' => $end
                                ])
                                ->where('id', '=', $found->id)
                                ->execute();
                        }
                    }
                }

                $progress = round($i / $total * 100);
                $this->callback('progress', 'setProgress', array($progress));
                $i++;
            }

            $this->callback('progress', 'done');
        }
    }
}
