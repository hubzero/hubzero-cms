<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication handler associations
 */
class HandlerAssoc extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_handler_assoc', 'id', $db);
	}

	/**
	 * Get associated handler(s)
	 *
	 * @param   integer  $vid        Publication Version ID
	 * @param   integer  $elementid  Element ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getAssoc($vid = null, $elementid = null)
	{
		if (!intval($vid) || !intval($elementid))
		{
			return false;
		}

		$query  = "SELECT H.*, A.params as configs, A.status, A.ordering FROM $this->_tbl as A ";
		$query .= " JOIN #__publication_handlers as H ON H.id=A.handler_id";
		$query .= " WHERE A.publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND A.element_id=" . $this->_db->quote($elementid);
		$query .= " ORDER BY A.ordering ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Load associated handler
	 *
	 * @param   integer  $vid        Publication Version ID
	 * @param   integer  $elementid  Element ID
	 * @param   string   $handler    Handler name
	 * @return  mixed    False if error, Object on success
	 */
	public function getAssociation($vid = null, $elementid = null, $handler = null)
	{
		if (!intval($vid) || !intval($elementid) || !$handler)
		{
			return false;
		}

		$query  = "SELECT H.*, A.params as configs, A.status, A.ordering FROM $this->_tbl as A ";
		$query .= " JOIN #__publication_handlers as H ON H.id=A.handler_id";
		$query .= " WHERE A.publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND A.element_id=" . $this->_db->quote($elementid);
		$query .= " AND H.name=" . $this->_db->quote($handler);
		$query .= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}
}
