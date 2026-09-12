<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Retire the Tools (Windows) resource type
 *
 * Added in 2016 for simulation tools delivered to a browser as a Windows
 * session. The platform does not do that any more, and the type has been
 * contributable = 0 the whole time, so no hub has ever been able to submit
 * one through the site - it only ever sat on the resources landing page
 * offering a category nobody could file anything under.
 *
 * Only where it is unused. A hub that has resources of this type has them
 * because somebody put them there deliberately, and taking the type out from
 * under them would leave rows pointing at nothing; those are left alone and
 * said so.
 **/
class Migration20260912091500ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__resource_types')) {
            return;
        }

        $id = $this->db->getQuery(true)
            ->select('id')
            ->from('#__resource_types')
            ->where('alias', '=', 'windowstools')
            ->value('id');

        if (!$id) {
            return;
        }

        $used = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__resources')
            ->where('type', '=', (int)$id)
            ->value('COUNT(*)');

        if ($used) {
            $this->log(
                'Leaving the Tools (Windows) resource type: ' . $used
                . ' resources still use it.'
            );
            return;
        }

        $this->db->getQuery(true)
            ->delete('#__resource_types')
            ->where('id', '=', (int)$id)
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__resource_types')) {
            return;
        }

        $id = $this->db->getQuery(true)
            ->select('id')
            ->from('#__resource_types')
            ->where('alias', '=', 'windowstools')
            ->value('id');

        if ($id) {
            return;
        }

        $this->db->getQuery(true)
            ->insert('#__resource_types')
            ->values([
                'alias'         => 'windowstools',
                'type'          => 'Tools (Windows)',
                'category'      => 27,
                'description'   => '<p>A simulation tool is software that allows users to run a '
                    . 'specific type of calculation. These are (MS) Windows-based.</p>',
                'customFields'  => '{"fields":[{"default":"","name":"credits","label":"Credits",'
                    . '"type":"textarea","required":"0"},{"default":"","name":"sponsoredby",'
                    . '"label":"Sponsors","type":"textarea","required":"0"},{"default":"",'
                    . '"name":"references","label":"References","type":"textarea","required":"0"}]}',
                'contributable' => 0,
                'params'        => '{"plg_about":"1","plg_citations":"0","plg_findthistext":"0",'
                    . '"plg_groups":"1","plg_questions":"1","plg_related":"0","plg_reviews":"1",'
                    . '"plg_share":"1","plg_sponsors":"1","plg_supportingdocs":"0","plg_usage":"0",'
                    . '"plg_versions":"0","plg_wishlist":"1"}'
            ])
            ->execute();
    }
}
