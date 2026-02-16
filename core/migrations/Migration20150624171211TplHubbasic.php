<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing site Hubbasic template
**/
class Migration20150624171211TplHubbasic extends Base
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
                ->where('client_id', '=', 0)
                ->where('home', '=', 1);
            if ($template = $query->value('template')) {
                if ($template == 'hubbasic') {
                    $this->db->getQuery(true)
                        ->update('#__template_styles')
                        ->set(['home' => 1])
                        ->where('client_id', '=', 0)
                        ->where('template', '=', 'hubbasic2013')
                        ->execute();
                }
            }
        }

        $this->deleteTemplateEntry('hubbasic', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addTemplateEntry('hubbasic', 'Hubbasic', 0, 1, 0);
    }
}
