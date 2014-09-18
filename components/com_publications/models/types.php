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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Publication master type helper class
 */
class PublicationTypesHelper extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_database       = NULL;

	/**
	 * Project
	 *
	 * @var object
	 */
	var $_project      	 = NULL;

	/**
	 * Base alias
	 *
	 * @var string
	 */
	var $_base   		 = NULL;

	/**
	 * Helper
	 *
	 * @var object
	 */
	var $_helper   		 = NULL;

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return     void
	 */
	public function __construct( &$db, $project = NULL )
	{
		$this->_database = $db;
		$this->_project  = $project;
	}

	/**
	 * Get all avail types
	 *
	 * @return     void
	 */
	public function getTypes()
	{
		$dir  = JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'types';

		$types = scandir($dir);
		$bases = array();

		foreach ($types as $t)
		{
			if (is_file($dir . DS . $t))
			{
				$bases[] = str_replace('.php', '', $t);
			}
		}

		return $bases;
	}

	/**
	 * Dispatch by attachment type
	 *
	 * @return     void
	 */
	public function dispatchByType( $type = NULL, $task = NULL, $data = array() )
	{
		if ($type === NULL)
		{
			return false;
		}

		$dir  = JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'types';

		$types = scandir($dir);

		foreach ($types as $t)
		{
			if (is_file($dir . DS . $t))
			{
				require_once($dir . DS . $t);

				$base = str_replace('.php', '', $t);
				$helperName = 'type' . ucfirst($base);

				$helper = new $helperName($this->_database, $this->_project, $data);
				if ($helper->_attachmentType == $type)
				{
					return $this->dispatch ($base, $task, $data);
				}

			}
		}

		return false;
	}

	/**
	 * Dispatch
	 *
	 * @return     void
	 */
	public function dispatch( $base = NULL, $task = NULL, $data = array() )
	{
		$output 		 = NULL;

		if ($base === NULL || $task === NULL)
		{
			return false;
		}

		// Default to files
		if (!is_file(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS
			. 'models' . DS . 'types' . DS . $base . '.php'))
		{
			$base = 'files';
		}
		$this->_base 	 = $base;

		if (is_file(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS
			. 'models' . DS . 'types' . DS . $base . '.php'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'types' . DS . $base . '.php');

			$helperName = 'type' . ucfirst($base);

			$this->_helper = new $helperName($this->_database, $this->_project, $data);

			// Task routing
			switch ( $task )
			{
				case 'checkVersionDuplicate':
					$output = $this->_checkVersionDuplicate();
					break;

				case 'checkDuplicate':
					$output = $this->_checkDuplicate();
					break;

				case 'parseSelections':
					$output = $this->_parseSelections();
					break;

				case 'getMainProperty':
					$output = $this->_getProperty('_attProperties', true);
					break;

				case 'getProperty':
					$output = $this->_getProperty();
					break;

				case 'getHelper':
					return $this->_helper;
					break;

				default:
					$output = $this->_helper->dispatch($task);
					break;
			}
		}
		else
		{
			return false;
		}

		return $output;
	}

	/**
	 * Check Duplicate (_showOptions function in plg_projects_publications)
	 *
	 * @return     void
	 */
	protected function _checkDuplicate()
	{
		$selections 	= $this->_helper->__get('selections');
		$pid			= $this->_helper->__get('pid');
		$attachmentType	= $this->_helper->_attachmentType;
		$attProperties	= $this->_helper->_attProperties;
		$attPrimeProp	= 'A.' .$attProperties[0];

		if (!$this->_project)
		{
			return false;
		}

		$query = "SELECT DISTINCT P.id, V.title, A.path FROM #__publications AS P ";
		$query.= " JOIN #__publication_attachments AS A ON A.publication_id = P.id ";
		$query.= " JOIN #__publication_versions AS V ON A.publication_id = V.publication_id AND V.main=1 ";
		$query.= " WHERE P.id != ".$pid;

		if (!$selections)
		{
			return false;
		}

		if (isset($selections[$this->_base]) && !empty($selections[$this->_base]))
		{
			$ids = '';
			foreach ($selections[$this->_base] as $sel)
			{
				$ids .= '"'.$sel.'",';
			}
			$ids = substr($ids, 0, strlen($ids) - 1);

			$query.= " AND $attPrimeProp IN(" . $ids . ")  ";
		}

		$query.= " AND A.type='" . $attachmentType . "' AND A.role=1 AND P.project_id=" . $this->_project->id;
		$query.= " GROUP BY P.id";

		$this->_database->setQuery( $query );
		return $this->_database->loadObjectList();

	}

	/**
	 * checkVersionDuplicate (_showOptions function in plg_projects_publications)
	 *
	 * @return     void
	 */
	protected function _checkVersionDuplicate()
	{
		$info = array();

		$selections 	= $this->_helper->__get('selections');
		$vid			= $this->_helper->__get('vid');
		$pid			= $this->_helper->__get('pid');
		$count 			= isset($selections['count']) ? $selections['count'] : 0;
		$attachmentType	= $this->_helper->_attachmentType;
		$attProperties	= $this->_helper->_attProperties;

		// Instantiate pub attachment
		$objPA = new PublicationAttachment( $this->_database );

		if ($selections && $vid && isset($selections[$this->_base]) && !empty($selections[$this->_base]))
		{
			foreach ($selections[$this->_base] as $sel)
			{
				if ($objPA->loadAttachment($vid, urldecode($sel), $attachmentType ))
				{
					$finfo = array();

					foreach ($attProperties as $prop)
					{
						$finfo[$prop] = $objPA->$prop;
					}
					$info[] = $finfo;
				}
			}
		}

		$result = $objPA->getVersionAttachments($pid, $vid);

		if (!$result)
		{
			return false;
		}
		else
		{
			$matched   = 0;
			foreach ($info as $o)
			{
				foreach ($result as $r)
				{
					$i = 0;
					foreach ($attProperties as $prop)
					{
						if ($o[$prop] == $r->$prop)
						{
							$i++;
						}
					}

					if ($i == 2 && $r->type ==$attachmentType)
					{
						$matched++;
					}
				}
			}

			if ($matched == $count)
			{
				return $result[0]->version_label;
			}
		}

		return false;
	}

	/**
	 * Parse selections
	 *
	 * @return     array
	 */
	protected function _parseSelections()
	{
		$sels 			= $this->_helper->__get('sels');
		$attachmentType	= $this->_helper->_attachmentType;
		$out 			= array();

		foreach ($sels as $sel)
		{
			$arr = explode("::", $sel);
			if ($arr[0] == $attachmentType)
			{
				$out[] = urldecode($arr[1]);
			}
		}

		return $out;
	}

	/**
	 * Get property
	 *
	 * @return     array
	 */
	protected function _getProperty($property = NULL, $firstKey = false)
	{
		$property = $property ? $property : $this->_helper->__get('property');
		$value = isset($this->_helper->$property) ? $this->_helper->$property : NULL;

		return ($firstKey && !empty($value[0])) ? $value[0] : $value;
	}
}
