<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tools (Windows) resource type
  *
**/
class Migration20160321145900ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            $id = $this->db->getQuery(true)
                ->select('id')
                ->from('#__resource_types')
                ->where('alias', '=', 'windowstools')
                ->value('id');

            if (!$id) {
                $description = '<p>A simulation tool is software that allows users to run a '
                    . 'specific type of calculation. These are (MS) Windows-based.</p>';
                $customFields = '{"fields":[{"default":"","name":"credits","label":"Credits",'
                    . '"type":"textarea","required":"0"},{"default":"","name":"sponsoredby",'
                    . '"label":"Sponsors","type":"textarea","required":"0"},{"default":"",'
                    . '"name":"references","label":"References","type":"textarea","required":"0"}]}';
                $params = '{"plg_about":"1","plg_citations":"0","plg_findthistext":"0",'
                    . '"plg_groups":"1","plg_questions":"1","plg_related":"0","plg_reviews":"1",'
                    . '"plg_share":"1","plg_sponsors":"1","plg_supportingdocs":"0","plg_usage":"0",'
                    . '"plg_versions":"0","plg_wishlist":"1"}';

                $this->db->getQuery(true)
                    ->insert('#__resource_types')
                    ->values([
                        'alias'         => 'windowstools',
                        'type'          => 'Tools (Windows)',
                        'category'      => 27,
                        'description'   => $description,
                        'customFields'  => $customFields,
                        'contributable' => 0,
                        'params'        => $params
                    ])
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            $id = $this->db->getQuery(true)
                ->select('id')
                ->from('#__resource_types')
                ->where('alias', '=', 'windowstools')
                ->value('id');

            if ($id) {
                $this->db->getQuery(true)
                    ->delete('#__resource_types')
                    ->where('id', '=', (int)$id)
                    ->execute();
            }
        }
    }
}
