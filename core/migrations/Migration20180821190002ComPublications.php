<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
/**
 * Migration script for initializing values of DOI service switch, DataCite DOI Service URL
 * and username/password, and EZID DOI Service URL and username/password
 *
*/
class Migration20180821190002ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $results = $this->db->getQuery(true)
            ->select(['extension_id', 'params'])
            ->from('#__extensions')
            ->where('name', '=', 'com_publications')
            ->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $r) {
                $params = json_decode($r->params);
                if (!isset($params->datacite_ezid_doi_service_switch)) {
                    $params->datacite_ezid_doi_service_switch = 1;
                }
                if (!isset($params->datacite_doi_service)) {
                    $params->datacite_doi_service = "https://mds.datacite.org";
                }
                if (!isset($params->ezid_doi_service)) {
                    $params->ezid_doi_service = "https://ezid.lib.purdue.edu";
                }
                if (!isset($params->ezid_doi_userpw)) {
                    $params->ezid_doi_userpw = "purr:#purrisice#";
                }
                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => json_encode($params)])
                    ->where('extension_id', '=', $r->extension_id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // No down method applicable.
    }
}
