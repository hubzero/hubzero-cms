<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Give the datasets resource type a capital letter
 *
 * Every other type a hub offers is written as a title - Documents, Seminars,
 * Series, Teaching Materials - and this one has been lower case since it was
 * added, so a resources landing page reads "Documents, Seminars, datasets".
 *
 * The name only. The alias is what URLs are built from and what anything
 * looking a type up by name uses, and that stays as it is.
 **/
class Migration20260912090000ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__resource_types')) {
            return;
        }

        $this->db->setQuery(
            "UPDATE `#__resource_types` SET `type` = 'Datasets'
             WHERE `alias` = 'datasets' AND `type` = 'datasets'"
        );
        $this->db->query();
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__resource_types')) {
            return;
        }

        $this->db->setQuery(
            "UPDATE `#__resource_types` SET `type` = 'datasets'
             WHERE `alias` = 'datasets' AND `type` = 'Datasets'"
        );
        $this->db->query();
    }
}
