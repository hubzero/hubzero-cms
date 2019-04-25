<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for defaulting the option to Basic search
 **/
class Migration20160606162915ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$parameter = Component::params('com_search')->get('engine');
		if ($parameter == 'hubgraph')
		{
			$query = "SELECT extension_id, params FROM #__extensions WHERE name = 'com_search';";
			$this->db->setQuery($query);
			$result = $this->db->loadAssoc();
			if (isset($result))
			{
				$parameters = json_decode($result['params']);
				$parameters->engine = 'basic';
				$parameters = json_encode($parameters);

				$query = "UPDATE #__extensions SET params=" . $this->db->quote($parameters) . " WHERE extension_id=" . $result['extension_id'] . ";";
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
