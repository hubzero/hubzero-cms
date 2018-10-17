<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;
use Filesystem;
use Lang;

include_once(dirname(__FILE__) . DS . 'attachment.php');
include_once(dirname(__FILE__) . DS . 'status.php');

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');

/**
 * Publications attachments class
 *
 */
class Attachments extends Obj
{
	/**
	 * Database
	 *
	 * @var object
	 */
	public $_db = null;

	/**
	 * Loaded elements
	 *
	 * @var  array
	 */
	protected $_types = array();

	/**
	 * Directories, where attachment types can be stored
	 *
	 * @var  array
	 */
	protected $_path = array();

	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db = $db;
		$this->_path[] = dirname(__FILE__) . DS . 'attachments';
	}

	/**
	 * Get attachments connector
	 *
	 * @param   string  $name
	 * @return  mixed   bool or object
	 */
	public function connector($name)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}
		return $type->getConnector();
	}

	/**
	 * Get status for an attachment within publication
	 *
	 * @param   string   $name
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   array    $attachments
	 * @return  object
	 */
	public function getStatus($name, $element = null, $elementId = 0, $attachments = null)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			$status = new \Components\Publications\Models\Status();
			$status->setError(Lang::txt('Attachment type not found'));
		}
		else
		{
			$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;

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
	 * @param   string   $name
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $params
	 * @param   object   $oldVersion
	 * @param   object   $newVersion
	 * @return  void
	 */
	public function transferData($name, $element = null, $elementId = 0, $pub = null, $params = null, $oldVersion, $newVersion)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			$status->setError(Lang::txt('Attachment type not found'));
		}
		else
		{
			$attachments = $pub->_attachments;
			$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;

			// Sort out attachments for this element
			$attachments = self::getElementAttachments($elementId, $attachments, $name);
			if ($attachments)
			{
				$type->transferData(
					$element->params,
					$elementId,
					$pub,
					$params,
					$attachments,
					$oldVersion,
					$newVersion
				);
			}
		}
	}

	/**
	 * Attach items to publication
	 *
	 * @param   string   $name
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $params
	 * @return  boolean
	 */
	public function attach($name, $element = null, $elementId = 0, $pub = null, $params = null)
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
	 * @param   string   $name
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $params
	 * @param   integer  $itemId
	 * @return  mixed    string or boolean
	 */
	public function serve($name = null, $element = null, $elementId = 0, $pub = null, $params = null, $itemId = null)
	{
		if ($name === null)
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
	 * @param   array    $elements
	 * @param   object   $pub
	 * @param   boolean  $authorized
	 * @param   string   $append
	 * @return  mixed    string or bool
	 */
	public function listItems($elements = null, $pub = null, $authorized = true, $append = null)
	{
		if (empty($elements) || $pub === null)
		{
			return false;
		}

		$output = '<ul class="element-list">';
		$i = 0;
		$links = '';
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
						 ? $attachments['elements'][$element->id] : null;

			if ($attachments)
			{
				$i++;
			}
			// Draw link(s)
			$links .= $type->drawList(
				$attachments,
				$element->manifest,
				$element->id,
				$pub,
				$element->block,
				$authorized
			);
		}
		$output .= $links;
		$output .= $append;
		$output .= '</ul>';

		return trim($links) ? $output : false;
	}

	/**
	 * Draw launching button/link for element
	 *
	 * @param   string   $name
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   object   $element
	 * @param   array    $elements
	 * @param   boolean  $authorized
	 * @return  mixed    object or boolean
	 */
	public function drawLauncher($name = null, $pub = null, $element = null, $elements = null, $authorized = true)
	{
		if ($name === null || $element === null || $pub === null)
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
		return $type->drawLauncher($element->manifest, $element->id, $pub, $element->block, $elements, $authorized);
	}

	/**
	 * Draws attachment
	 *
	 * @param   string  $name
	 * @param   array   $data
	 * @param   object  $typeParams
	 * @param   object  $handler
	 * @return  mixed   object or bool
	 */
	public function drawAttachment($name, $data = null, $typeParams = null, $handler = null)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}
		if (!$data)
		{
			return false;
		}

		// Draw
		return $type->drawAttachment($data, $typeParams, $handler);
	}

	/**
	 * Draws attachment
	 *
	 * @param   string   $name
	 * @param   object   $attachment
	 * @param   object   $view
	 * @param   integer  $ordering
	 * @return  mixed    object or bool
	 */
	public function buildDataObject($name, $attachment, $view, $ordering = 1)
	{
		// Load attachment type
		$type = $this->loadAttach($name);

		if ($type === false)
		{
			return false;
		}

		// Draw
		return $type->buildDataObject($attachment, $view, $ordering);
	}

	/**
	 * Update attachment record
	 *
	 * @param   string   $name
	 * @param   object   $row
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @param   object   $element
	 * @param   object   $params
	 * @return  boolean
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
	 * @param   string   $name
	 * @param   object   $row
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @param   object   $element
	 * @param   object   $params
	 * @return  boolean
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
	 * @param   integer  $elementId
	 * @param   array    $attachments
	 * @param   string   $type
	 * @param   string   $role
	 * @param   boolean  $includeUnattached
	 * @return  object
	 */
	public function getElementAttachments($elementId = 0, $attachments = array(), $type = '', $role = '', $includeUnattached = true)
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
	 * @param   string   $name
	 * @param   boolean  $new
	 * @return  object
	 */
	public function loadAttach($name, $new = false)
	{
		$signature = md5($name);

		if ((isset($this->_types[$signature])
			&& !($this->_types[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return $this->_types[$signature];
		}

		$elementClass = __NAMESPACE__ . '\\Attachment\\' . ucfirst($name);
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

			$file = Filesystem::clean(str_replace('_', DS, $name) . '.php');

			if ($elementFile = Filesystem::find($dirs, $file))
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

	/**
	 * Bundle elements
	 *
	 * @param   object  $zip
	 * @param   array   $elements
	 * @param   object  $pub
	 * @param   string  $readme
	 * @param   string  $bundleDir
	 * @return  mixed   object or bool
	 */
	public function bundleItems($zip = null, $elements = null, $pub = null, &$readme, $bundleDir)
	{
		if ($zip === null || empty($elements) || $pub === null)
		{
			return false;
		}

		foreach ($elements as $element)
		{
			// File?
			if ($element->manifest->params->type != 'file')
			{
			//	continue;
			}

			// Load attachment type
			$type = $this->loadAttach($element->manifest->params->type);

			if ($type === false)
			{
				return false;
			}

			$attachments = $pub->_attachments;
			$attachments = isset($attachments['elements'][$element->id])
						 ? $attachments['elements'][$element->id] : null;

			// Add to bundle
			$type->addToBundle(
				$zip,
				$attachments,
				$element->manifest,
				$element->id,
				$pub,
				$element->block,
				$readme,
				$bundleDir
			);
		}
		return;
	}

	/**
	 * Show bundle elements
	 *
	 * @param   array   $elements
	 * @param   object  $pub
	 * @return  mixed   object or bool
	 */
	public function showPackagedItems($elements = null, $pub = null)
	{
		if (empty($elements) || $pub === null)
		{
			return false;
		}

		$contents = null;
		foreach ($elements as $element)
		{
			// File?
			if ($element->manifest->params->type != 'file')
			{
			//	continue;
			}

			// Load attachment type
			$type = $this->loadAttach($element->manifest->params->type);

			if ($type === false)
			{
				return false;
			}

			$attachments = $pub->_attachments;
			$attachments = isset($attachments['elements'][$element->id])
						 ? $attachments['elements'][$element->id] : null;

			// Add to bundle
			$contents .= $type->drawPackageList(
				$attachments,
				$element->manifest,
				$element->id,
				$pub,
				$element->block,
				true
			);
		}
		return $contents;
	}
}
