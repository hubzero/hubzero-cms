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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models;

use Hubzero\Utility\Str;
use Request;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'post.php');
require_once(__DIR__ . DS . 'item.php');

/**
 * Collections model for a post
 */
class Post extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Collections\\Tables\\Post';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_collections.post.description';

	/**
	 * Adapter
	 *
	 * @var object
	 */
	private $_adapter = null;

	/**
	 * Post
	 *
	 * @var object
	 */
	private $_data = null;

	/**
	 * CollectionsModelCollection
	 *
	 * @var object
	 */
	private $_collection = null;

	/**
	 * Returns a reference to this model
	 *
	 * @param   mixed   $oid  Integer, string, object or array
	 * @return  object
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Bind data to the model
	 *
	 * @param   mixed    $data  Object or array
	 * @return  boolean  True on success, False on error
	 */
	public function bind($data=null)
	{
		$item = new \stdClass;

		if (is_object($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (get_object_vars($data) as $key => $property)
				{
					if (substr($key, 0, strlen('item_')) == 'item_')
					{
						$nk = substr($key, strlen('item_'));
						$item->$nk = $property;
						continue;
					}
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $property);
					}
				}
			}
		}
		else if (is_array($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (array_keys($data) as $key)
				{
					if (substr($key, 0, strlen('item_')) == 'item_')
					{
						$nk = substr($key, strlen('item_'));
						$item->$nk = $data[$key];
						continue;
					}
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $data[$key]);
					}
				}
			}
		}
		else
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Data must be of type object or array. Type given was %s', gettype($data))
			);
			throw new \InvalidArgumentException(Lang::txt('Data must be of type object or array. Type given was %s', gettype($data)));
		}

		$this->item($item);

		return $res;
	}

	/**
	 * Get the item for this post
	 *
	 * @param   integer  $oid
	 * @return  object
	 */
	public function item($oid=null)
	{
		if (!($this->_data instanceof Item))
		{
			if ($oid === null)
			{
				$oid = $this->get('item_id', 0);
			}

			$this->_data = new Item($oid);
			/*if ($d = $this->description('raw'))
			{
				$this->_data->set('description', $this->get('description'));
			}*/
		}

		return $this->_data;
	}

	/**
	 * Get the item for this post
	 *
	 * @param   integer $oid
	 * @return  object
	 */
	public function collection($oid=null)
	{
		if (!($this->_collection instanceof Collection))
		{
			if ($oid === null)
			{
				$oid = $this->get('collection_id', 0);
			}

			$this->_collection = new Collection($oid);
		}

		return $this->_collection;
	}

	/**
	 * Check if the post is the original (first) post
	 *
	 * @return  boolean  True if original, false if not
	 */
	public function original()
	{
		if ((int) $this->get('original') > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Remove a post
	 *
	 * @return  boolean  True on success, false on error
	 */
	public function remove()
	{
		if ($this->original())
		{
			$this->setError(Lang::txt('Original posts must be deleted or moved.'));
			return false;
		}

		if (!$this->_tbl->delete($this->get('id')))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Move a post
	 *
	 * @return  boolean True on success, false on error
	 */
	public function move($collection_id)
	{
		$collection_id = intval($collection_id);

		if (!$collection_id)
		{
			$this->setError(Lang::txt('Empty collection ID.'));
			return false;
		}

		$this->set('collection_id', $collection_id);

		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get the URL for this post
	 *
	 * @param   string $type   The type of link to return
	 * @param   mixed  $params Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->_adapter()->build($type, $params);
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$scope = strtolower($this->get('object_type'));
			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . DS . 'adapters' . DS . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s"', $scope));
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('object_id'));
			$this->_adapter->set('id', $this->get('id'));
			$this->_adapter->set('alias', $this->get('alias'));
		}
		return $this->_adapter;
	}

	/**
	 * Get the content of the entry
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function description($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('description.parsed', null);
				if ($content === null)
				{
					$config = array(
						'option'   => $this->get('option', Request::getCmd('option', 'com_collections')),
						'scope'    => 'collections',
						'pagename' => 'collections',
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => 'collection'
					);

					$content = stripslashes((string) $this->get('description', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('description.parsed', (string) $this->get('description', ''));
					$this->set('description', $content);

					return $this->description($as, $shorten);
				}
				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->description('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('description'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = Str::truncate($content, $shorten);
		}
		return $content;
	}
}
