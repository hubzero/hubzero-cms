<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing site Hubbasicadmin template
  *
**/
class Migration20150624164611TplHubbasicadmin extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__template_styles')) {
            $query = $this->db->getQuery(true)
                ->select('template')
                ->from('#__template_styles')
                ->where('client_id', '=', 1)
                ->where('home', '=', 1);
            $template = $query->value('template');

            if ($template == 'hubbasicadmin') {
                $this->db->getQuery(true)
                    ->update('#__template_styles')
                    ->set(['home' => 1])
                    ->where('client_id', '=', 1)
                    ->where('template', '=', 'kameleon')
                    ->execute();
            }
        }

        $this->deleteTemplateEntry('hubbasicadmin', 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addTemplateEntry('hubbasicadmin', 'Hubbasicadmin', 1, 1, 0);
    }
}
