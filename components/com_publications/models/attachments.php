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

include_once(dirname(__FILE__) . DS . 'attachment.php');
include_once(dirname(__FILE__) . DS . 'status.php');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'attachment.php');

/**
 * Publications attachments class
 *
 */
class PublicationsModelAttachments extends JObject
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
	protected $_types 	= array();

	/**
	* @var    array  Directories, where attachment types can be stored
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
		$this->_path[] 	= dirname(__FILE__) . DS . 'attachments';
	}

	/**
	 * Get status for an attachment within publication
	 *
	 * @return object
	 */
	public function getStatus($name, $element = NULL, $elementId = 0, $attachments = NULL)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			$status = new PublicationsModelStatus();
			$status->setError(JText::_('Attachment type not found') );
		}
		else
		{
			$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;

			// Sort out attachments for this element
			$attachments = self::getElementAttachments($elementId, $attachments, $name);

			$status = $type->getStatus($element, $attachments);
		}

		// Return status
		return $status;
	}

	/**
	 * Transfer data
	 *
	 * @return boolean
	 */
	public function transferData($name, $element = NULL, $elementId = 0,
		$pub = NULL, $params = NULL, $oldVersion, $newVersion)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			$status->setError(JText::_('Attachment type not found') );
		}
		else
		{
			$attachments = $pub->_attachments;
			$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;

			// Sort out attachments for this element
			$attachments = self::getElementAttachments($elementId, $attachments, $name);

			$type->transferData($element->params, $elementId, $pub, $params,
				$attachments, $oldVersion, $newVersion
			);
		}

	}

	/**
	 * Attach items to publication
	 *
	 * @return object
	 */
	public function attach($name, $element = NULL, $elementId = 0, $pub = NULL, $params = NULL)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}

		// Save incoming selections
		if ($type->save($element, $elementId, $pub, $params))
		{
			if ($type->get('_message'))
			{
				$this->set('_message', $type->get('_message'));
			}

			return true;
		}

		return false;

	}

	/**
	 * Serve attachments within element
	 *
	 * @return object
	 */
	public function serve($name = NULL, $element = NULL,
		$elementId = 0, $pub = NULL, $params = NULL, $itemId = NULL)
	{
		if ($name === NULL)
		{
			return false;
		}

		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}

		// Serve attachments
		if ($content = $type->serve($element, $elementId, $pub, $params, $itemId))
		{
			if ($type->get('_message'))
			{
				$this->set('_message', $type->get('_message'));
			}

			return $content;
		}

		return false;
	}

	/**
	 * Draw list of element items
	 *
	 * @return object
	 */
	public function listItems($elements = NULL, $pub = NULL, $authorized = true)
	{
		if (empty($elements) || $pub === NULL)
		{
			return false;
		}

		$output = '<ul class="element-list">';
		$i = 0;
		foreach ($elements as $element)
		{
			// Load attachment type
			$type = $this->loadAttach($element->manifest->params->type);

			if ($type === false)
			{
				return false;
			}

			$attachments = $pub->_attachments;
			$attachments = isset($attachments['elements'][$element->id])
						 ? $attachments['elements'][$element->id] : NULL;

			if ($attachments)
			{
				$i++;
			}
			// Draw link(s)
			$output .= $type->drawList(
				$attachments,
				$element->manifest,
				$element->id,
				$pub,
				$element->block,
				$authorized
			);
		}

		$output .= '</ul>';

		return $i > 0 ? $output : false;
	}

	/**
	 * Draw launching button/link for element
	 *
	 * @return object
	 */
	public function drawLauncher($name = NULL, $pub = NULL, $element = NULL, $authorized = true)
	{
		if ($name === NULL || $element === NULL || $pub === NULL)
		{
			return false;
		}

		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}

		// Draw link
		return $type->drawLauncher($element->manifest, $element->id, $pub, $element->block, $authorized);
	}

	/**
	 * Update attachment record
	 *
	 * @return object
	 */
	public function update($name, $row, $pub, $actor, $elementId, $element, $params)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}

		// We do need attachment record
		if (!$row || !$row->id)
		{
			return false;
		}

		// Save incoming info
		if ($type->updateAttachment($row, $element->params, $elementId, $pub, $params))
		{
			if ($type->get('_message'))
			{
				$this->set('_message', $type->get('_message'));
			}

			return true;
		}

		return false;

	}

	/**
	 * Remove attachment
	 *
	 * @return object
	 */
	public function remove($name, $row, $pub, $actor, $elementId, $element, $params)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}

		// We do need attachment record
		if (!$row || !$row->id)
		{
			return false;
		}

		// Save incoming info
		if ($type->removeAttachment($row, $element->params, $elementId, $pub, $params))
		{
			if ($type->get('_message'))
			{
				$this->set('_message', $type->get('_message'));
			}

			return true;
		}

		return false;
	}

	/**
	 * Get element attachments (ween out inapplicable attachments)
	 *
	 * @return  object
	 */
	public function getElementAttachments( $elementId = 0, $attachments = array(),
		$type = '', $role = '', $includeUnattached = true )
	{
		$collect = array();

		if (!$attachments || !$elementId)
		{
			return $attachments;
		}

		foreach ($attachments as $attach)
		{
			// Fix up supporting docs
			$attach->role = $attach->role ? $attach->role : 2;

			// Skip items in different role
			if ($role && ($attach->role != $role))
			{
				continue;
			}

			// Skip items of different type
			if ($type && $attach->type != $type)
			{
				continue;
			}

			// Collect
			if (($attach->element_id == $elementId) || ($includeUnattached == true && !$attach->element_id))
			{
				$collect[] = $attach;
			}
		}

		return $collect;
	}

	/**
	 * Loads a block
	 *
	 * @return  object
	 */
	public function loadAttach( $name, $new = false )
	{
		$signature = md5($name);

		if ((isset($this->_types[$signature])
			&& !($this->_types[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return	$this->_types[$signature];
		}

		$elementClass = 'PublicationsModelAttachment' . ucfirst($name);
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

		$this->_types[$signature] = new $elementClass($this);
		return $this->_types[$signature];
	}
}
