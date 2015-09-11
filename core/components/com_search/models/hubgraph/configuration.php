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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Hubgraph;

use Exception;
use ArrayAccess;
use Iterator;

require_once(__DIR__ . DS . 'db.php');

/**
 * Hubgraph configuration class
 */
class Configuration implements ArrayAccess, Iterator
{
	/**
	 * Instance of Hubgraph config
	 *
	 * @var  object
	 */
	private static $inst;

	/**
	 * Default values
	 *
	 * @var  array
	 */
	private static $defaultSettings = array(
		'host'           => 'unix:///var/run/hubzero-hubgraph/hubgraph-server.sock',
		'port'           => NULL,
		'showTagCloud'   => TRUE,
		'enabledOptions' => ''
	);

	/**
	 * Current settings
	 *
	 * @var  array
	 */
	private $settings;

	/**
	 * Index
	 *
	 * @var  integer
	 */
	private $idx;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	private function __construct()
	{
		if (!$this->settings)
		{
			$this->settings = self::$defaultSettings;
		}
	}

	/**
	 * Get an instance of the configuration class
	 *
	 * @return  object
	 */
	public static function instance()
	{
		if (!self::$inst)
		{
			self::$inst = new self;
			self::$inst->settings = array();

			$params = self::$inst->params();
			foreach (self::$defaultSettings as $k => $v)
			{
				self::$inst->settings[$k] = $params->get('hubgraph_' . $k, $v);
			}

			self::$inst->validate();
		}
		return self::$inst;
	}

	/**
	 * Save configuration values to the database
	 *
	 * @return  void
	 */
	public function save()
	{
		$params = $this->params();
		foreach (self::$settings as $k => $v)
		{
			$params->set('hubgraph_' . $k, $v);
		}
		$params = $params->toString();

		$updateQuery = 'UPDATE `#__extensions` SET `params` = ? WHERE `type`=\'component\' AND `element` = \'com_search\'';
		$insertQuery = 'INSERT INTO `#__extensions` (`name`, `type`, `element`, `params`) VALUES (\'com_search\', \'component\', \'com_search\', ?)';

		if (!Db::update($updateQuery, array($params)))
		{
			Db::execute($insertQuery, array($params));
		}
	}

	/**
	 * Get component params
	 *
	 * @return  object
	 */
	public function params()
	{
		return \Component::params('com_search');
	}

	/**
	 * Validate settings
	 *
	 * @return  void
	 */
	private function validate()
	{
		foreach (self::$defaultSettings as $k => $v)
		{
			if (!array_key_exists($k, $this->settings))
			{
				$this->settings[$k] = $v;
			}
		}
	}

	/**
	 * Bind data to settings
	 *
	 * @return  object
	 */
	public function bind($form)
	{
		foreach (array_keys($this->settings) as $k)
		{
			if (array_key_exists($k, $form))
			{
				$this->settings[$k] = $form[$k] == '' ? NULL : $form[$k];
			}
		}
		return $this;
	}

	/**
	 * Check if an option is enabled
	 *
	 * @param   string   $opt
	 * @return  boolean
	 */
	public function isOptionEnabled($opt)
	{
		return in_array($opt, explode(',', $this->settings['enabledOptions']));
	}

	/**
	 * Normalize a key name
	 *
	 * @param   string  $k
	 * @return  string
	 */
	public static function niceKey($k)
	{
		return ucfirst(preg_replace_callback('/([A-Z])+/', function($ma)
		{
			return ' ' . strtolower($ma[1]);
		}, $k));
	}

	/**
	 * Set a value
	 *
	 * @param   string   $offset
	 * @param   mixed    $value
	 * @return  void
	 * @throws  Exception
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset) || !isset($this->settings[$offset]))
		{
			throw new Exception('Not supported');
		}
		$this->settings[$offset] = $value;
	}

	/**
	 * Check if a value is set
	 *
	 * @param   string   $offset
	 * @return  boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->settings[$offset]);
	}

	/**
	 * Unset a vlaue
	 *
	 * @return  void
	 * @throws  Exception
	 */
	public function offsetUnset($_offset)
	{
		throw new \Exception('not supported');
	}

	/**
	 * Get a set value
	 *
	 * @param   string  $offset
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		if (!$this->offsetExists($offset))
		{
			return NULL;
		}
		return $this->settings[$offset];
	}

	/**
	 * Rewind to beginning of the array
	 *
	 * @return  boolean
	 */
	public function rewind()
	{
		return reset($this->settings);
	}

	/**
	 * Return current item
	 *
	 * @return  mixed
	 */
	public function current()
	{
		return current($this->settings);
	}

	/**
	 * Return key for current item
	 *
	 * @return  mixed
	 */
	public function key()
	{
		return key($this->settings);
	}

	/**
	 * Return next item
	 *
	 * @return  mixed
	 */
	public function next()
	{
		return next($this->settings);
	}

	/**
	 * Check if key position is valid
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		return array_key_exists($this->key(), $this->settings);
	}
}
