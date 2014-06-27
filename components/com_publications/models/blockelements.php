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

include_once(dirname(__FILE__) . DS . 'blockelement.php');
include_once(dirname(__FILE__) . DS . 'status.php');

/**
 * Publications block elements class
 *
 */
class PublicationsModelBlockElements
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	public $_db   		= NULL;

	/**
	* @var    array  Loaded elements
	*/
	protected $_elements 	= array();

	/**
	* @var    array  Directories, where block elements can be stored
	*/
	protected $_path 	= array();

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db 		= $db;
		$this->_path[] 	= dirname(__FILE__) . DS . 'blockelements';
	}

	/**
	 * Get status for a block element within publication
	 *
	 * @return object
	 */
	public function getStatus($name, $manifest = NULL, $pub = NULL)
	{
		// Load attachment type
		$element = $this->loadElement($name);

		if ($element === false || !$pub || !$pub->id)
		{
			$status = new PublicationsModelStatus();
		}
		else
		{
			$status = $element->getStatus($manifest, $pub);
		}

		// Return status
		return $status;
	}

	/**
	 * Draw element for
	 *
	 * @return object
	 */
	public function drawElement($name, $elementId = 0, $manifest = NULL,
		$master = NULL, $pub = NULL, $status = NULL, $viewname = 'edit', $order = 0)
	{
		// Load attachment type
		$element = $this->loadElement($name);

		if ($element === false)
		{
			return false;
		}
		else
		{
			return $element->render($elementId, $manifest, $pub, $viewname, $status, $master, $order);
		}
	}

	/**
	 * Get active element
	 *
	 * @return object
	 */
	public function getActiveElement($elements, $review)
	{
		// What is the last incomplete element?
		$lastComplete 	= 0;
		$lastIncomplete = 0;
		$total 			= 0;
		$showElement 	= 1;
		$collector		= array();
		$i				= 1;

		foreach ($elements as $elId => $el)
		{
			$collector[$i] = $elId;
			if ($el->status == 1)
			{
				$lastComplete = $i;
			}
			if (!$lastIncomplete)
			{
				// Curator review?
				if (($review && $review->elements
					&& isset($review->elements->$elId))
					&& $el->status != 0)
				{
					$reviewStatus = $review->elements->$elId;
					if ($reviewStatus->status == 0 && !$reviewStatus->lastupdate)
					{
						$lastIncomplete = $i;
					}
					if ($reviewStatus->status >= 1)
					{
						$lastComplete = $i;
					}
				}
				elseif ($el->status != 1)
				{
					$lastIncomplete = $i;
				}
			}

			$total++;
			$i++;
		}

		$nextElement = isset($collector[$lastComplete + 1])
					 ? $collector[$lastComplete + 1] : $collector[$lastComplete];

		if ($lastIncomplete)
		{
			$showElement = $collector[$lastIncomplete];
		}
		else
		{
			$showElement = isset($elements->$nextElement)
						? $nextElement : $collector[$lastComplete];
		}

		return array('showElement' => $showElement, 'total' => $total);
	}

	/**
	 * Loads a block
	 *
	 * @return  object
	 */
	public function loadElement( $name, $new = false )
	{
		$signature = md5($name);

		if ((isset($this->_elements[$signature])
			&& !($this->_elements[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return	$this->_elements[$signature];
		}

		$elementClass = 'PublicationsModelBlockElement' . ucfirst($name);
		if (!class_exists($elementClass))
		{
			if (isset($this->_path))
			{
				$dirs = $this->_path;
			}
			else
			{
				$dirs = array();
			}

			$file = JFilterInput::getInstance()->clean(str_replace('_', DS, $name).'.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = JPath::find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				$false = false;
				return $false;
			}
		}

		if (!class_exists($elementClass))
		{
			$false = false;
			return $false;
		}

		$this->_elements[$signature] = new $elementClass($this);
		return $this->_elements[$signature];
	}
}
