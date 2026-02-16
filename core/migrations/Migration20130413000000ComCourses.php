<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for grade book unique index and form asset id reference
  *
**/
class Migration20130413000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        $runExtra = false;

        // Add a unique index on grade book
        $schema->addUniqueIndex('#__courses_grade_book', 'alternate_key', ['user_id', 'scope', 'scope_id']);

        // Add asset_id field to forms table
        if (!$schema->hasColumn('#__courses_forms', 'asset_id')) {
            $schema->addColumn('#__courses_forms', 'asset_id')->integer()->nullable()->default(null)->execute();
            $runExtra = true;
        }

        if ($runExtra) {
            // Get the form id from the asset content fields
            $rows = $this->db->getQuery(true)
                ->select(['id', 'content'])
                ->from('#__courses_assets')
                ->where('type', '=', 'exam')
                ->loadObjectList();

            // Now insert those into the new forms asset_id field
            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->update('#__courses_forms')
                    ->set(['asset_id' => $row->id])
                    ->where('id', '=', json_decode($row->content)->form_id)
                    ->whereIsNull('asset_id')
                    ->execute();
            }

            // Delete the content field for asset type of exam
            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['content' => ''])
                ->where('type', '=', 'exam')
                ->execute();
        }
    }
}
