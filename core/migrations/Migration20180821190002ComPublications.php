<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_HZEXEC_') or die();

/**
 * Migration script for initializing values of DOI service switch, DataCite DOI Service URL and username/password, and EZID DOI Service URL
 * and username/password
 **/
class Migration20180821190002ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id`, `params` FROM `#__extensions` WHERE `name` = 'com_publications'";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();
		if (count($results) > 0)
		{
			foreach ($results as $r)
			{
				$params = json_decode($r->params);
				if (!isset($params->datacite_ezid_doi_service_switch))
				{
					$params->datacite_ezid_doi_service_switch = 1;
				}
				if (!isset($params->datacite_doi_service))
				{
					$params->datacite_doi_service = "https://mds.datacite.org";
				}
				if (!isset($params->ezid_doi_service))
				{
					$params->ezid_doi_service = "https://ezid.lib.purdue.edu";
				}
				if (!isset($params->ezid_doi_userpw))
				{
					$params->ezid_doi_userpw = "purr:#purrisice#";
				}
				$query = "UPDATE `#__extensions` SET `params` = " . $this->db->quote(json_encode($params)) . " WHERE `extension_id` = " . $this->db->quote($r->extension_id);
				$this->db->setQuery($query);
				$this->db->query();
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
