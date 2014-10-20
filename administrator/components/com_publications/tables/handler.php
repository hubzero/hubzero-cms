<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for available publication handlers
 */
class PublicationHanlder extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       				= NULL;

	/**
	 * Name
	 *
	 * @var string
	 */
	var $name 					= NULL;

	/**
	 * Label
	 *
	 * @var string
	 */
	var $label 					= NULL;

	/**
	 * Title
	 *
	 * @var string
	 */
	var $title 					= NULL;

	/**
	 * About
	 *
	 * @var text
	 */
	var $about 					= NULL;

	/**
	 * Status
	 *
	 * @var integer
	 */
	var $status 				= NULL;

	/**
	 * Params
	 *
	 * @var text
	 */
	var $params 				= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_handlers', 'id', $db );
	}

	/**
	 * Load handler
	 *
	 * @param      string 	$name 	Alias name of handler
	 *
	 * @return     mixed False if error, Object on success
	 */
	public function loadRecord( $name = NULL )
	{
		if ($name === NULL)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE name='" . $name . "'";
		$query.= " LIMIT 1";
		$this->_db->setQuery( $query );

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Load handler config
	 *
	 * @param      string 	$name 	Alias name of handler
	 *
	 * @return     mixed False if error, Object on success
	 */
	public function getConfig( $name = NULL )
	{
		if ($name === NULL)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE name='" . $name . "'";
		$query.= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		// Parse configs
		if ($result)
		{
			$output = array();
			$output['params'] = array();
			foreach ($result[0] as $field => $value)
			{
				if ($field == 'params')
				{
					$params = json_decode($value, TRUE);
					if (is_array ($params))
					{
						foreach ($params as $paramName => $paramValue)
						{
							$output['params'][$paramName] = $paramValue;
						}
					}
				}
				else
				{
					$output[$field] = $value;
				}
			}

			return $output;
		}

		return false;
	}
}
