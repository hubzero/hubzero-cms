<?php

namespace Migrations;

use Hubzero\Content\Migration\Base;

// no direct access
/**
 * Migration script for updating Collections params.
  *
**/
class Migration20210105000001ComCollections extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            // Get the current params
            $params = Component::params('com_collections');
            $allow_comments = $params->get('allow_comments');

            // If the comments param is not set, set it to 1-Yes
            if (!isset($allow_comments)) {
                $params->set('allow_comments', 1);
                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params->toString()])
                    ->where('name', '=', 'com_collections')
                    ->where('element', '=', 'com_collections')
                    ->execute();
            }
        }
    }

    public function down()
    {
        // No need to do anything, just leave the unused param.
    }
}
