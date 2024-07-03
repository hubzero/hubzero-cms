<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication collaborator
 */
class Collaborator extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_collaborators', 'id', $db);
	}

	/**
	 * Get collaborator by name
	 *
	 * @param   string  $name
	 *
	 * @return  object or false
	 */
	public function getCollaboratorByName($name)
	{
		if (!$name)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE name=" . $this->_db->quote($name);
		$this->_db->setQuery($query);
		
		return $this->_db->loadObject();
	}
	
	/**
	 * Save ORCID Record
	 *
	 * @param   string  $name
	 * @param   string  $orcid
	 * @param   string  $accessToken
	 *
	 * @return  object or false
	 */
	public function saveORCIDRecord($name, $orcid, $accessToken)
	{
		if (empty($name) || empty($orcid) || empty($accessToken))
		{
			return false;
		}
		
		$record = $this->getCollaboratorByName($name);
		
		if (empty($record))
		{
			$query = "INSERT INTO $this->_tbl (id, name, orcid, access_token, acquisition_date) VALUES (" . $this->_db->quote($name) . "," . $this->_db->quote($orcid) . "," . $this->_db->quote($accessToken) . "," . $this->_db->quote(date('Y-m-d H:i:s')) . ")";
		}
		else
		{
			if (empty($record->orcid) && empty($record->access_token))
			{
				$query = "UPDATE $this->_tbl SET orcid=" . $this->_db->quote($orcid) . " AND access_token=" . $this->_db->quote($accessToken) . " WHERE name=" . $this->_db->quote($name);
			}
		}
		
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}
