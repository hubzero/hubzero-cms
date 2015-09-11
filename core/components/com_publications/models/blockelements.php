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

use Hubzero\Base\Object;

include_once(dirname(__FILE__) . DS . 'blockelement.php');
include_once(dirname(__FILE__) . DS . 'status.php');

/**
 * Publications block elements class
 *
 */
class BlockElements extends Object
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
			$status = new \Components\Publications\Models\Status();
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
				if ($review && $review->elements
					&& isset($review->elements->$elId) && $review->elements->$elId->status != 2)
				{
					$reviewStatus = $review->elements->$elId;
					if ($reviewStatus->status == 0 && !$reviewStatus->lastupdate)
					{
						$lastIncomplete = $i;
					}
					if ($reviewStatus->status == 1)
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

		$elementClass = __NAMESPACE__ . '\\BlockElement\\' . ucfirst($name);
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

			$file = \JFilterInput::getInstance()->clean(str_replace('_', DS, $name).'.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = \JPath::find($dirs, $file))
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
