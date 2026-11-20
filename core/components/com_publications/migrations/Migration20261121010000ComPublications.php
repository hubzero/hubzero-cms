<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Point publication license icons at the files where they are now
 *
 * Migration20170902000000ComPublications seeded the custom, Creative Commons
 * and standard licenses with icons under /components/com_publications/, a
 * path from before core/ and site/ existed; the submission form's license
 * choices showed broken images. The logos are now in
 * /core/components/com_publications/site/assets/img/logos/.
 *
 * There is no down(): the old paths name no file.
 */
class Migration20261121010000ComPublications extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        if (!$this->db->tableExists('#__publication_licenses')) {
            return;
        }

        $this->db->setQuery(
            "SELECT `id`, `icon` FROM `#__publication_licenses`"
            . " WHERE `icon` LIKE '/components/com_publications/%/logos/%'"
        );

        foreach ($this->db->loadObjectList() ?: [] as $license) {
            $icon = '/core/components/com_publications/site/assets/img/logos/' . basename((string) $license->icon);

            $query = $this->db->getQuery()
                ->update('#__publication_licenses')
                ->set(['icon' => $icon])
                ->whereEquals('id', (int) $license->id);
            $this->db->setQuery($query->toString());
            $this->db->query();
        }
    }
}
