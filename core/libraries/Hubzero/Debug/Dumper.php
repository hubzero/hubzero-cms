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

namespace Hubzero\Debug;

use Hubzero\Debug\Dumper\Renderable;
use Hubzero\Debug\Dumper\RendererNotFoundException;
use InvalidArgumentException;

class Dumper
{
	/**
	 * Renderable
	 *
	 * @var  object
	 */
	protected $_renderer = null;

	/**
	 * Messages
	 *
	 * @var  array
	 */
	protected $_messages = array();

	/**
	 * Map of characters to be replaced through strtr
	 *
	 * @var  array
	 */
	protected $_nameReplacements = array(
		'-'  => '',
		'_'  => '',
		' '  => '',
		'\\' => '',
		'/'  => ''
	);

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->clear();
	}

	/**
	 * Returns a reference to a global object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  object
	 */
	public static function &getInstance()
	{
		static $instance;

		if (!$instance)
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Returns the renderer
	 *
	 * @return  object  Renderer\Renderable
	 */
	public function getRenderer()
	{
		return $this->_renderer;
	}

	/**
	 * Sets the renderer
	 *
	 * @param   mixed  $renderer  string or Renderable
	 * @return  object
	 */
	public function setRenderer($renderer)
	{
		if (is_string($renderer))
		{
			$cName = $this->scrub($renderer);

			$invokable = __NAMESPACE__ . '\\Dumper\\' . ucfirst($cName);

			if (!class_exists($invokable))
			{
				throw new RendererNotFoundException(sprintf(
					'%s: failed retrieving renderer via invokable class "%s"; class does not exist',
					__CLASS__ . '::' . __FUNCTION__,
					$invokable
				));
			}
			$renderer = new $invokable;
		}

		if (!($renderer instanceof Renderable))
		{
			throw new InvalidArgumentException(sprintf(
				'%s was unable to fetch renderer or renderer was not an instance of %s',
				get_class($this) . '::' . __FUNCTION__,
				__NAMESPACE__ . '\\Dumper\\Renderable'
			));
		}

		$this->_renderer = $renderer;

		return $this;
	}

	/**
	 * Canonicalize name
	 *
	 * @param   string  $name
	 * @return  string
	 */
	protected function scrub($name)
	{
		// this is just for performance instead of using str_replace
		return strtolower(strtr($name, $this->_nameReplacements));
	}

	/**
	 * Adds a message
	 *
	 * @param   mixed   $message
	 * @param   string  $label
	 * @return  object
	 */
	public function addVar($var)
	{
		$varc = (is_object($var) ? clone $var : $var);
		$this->_messages[] = array(
			'var'  => $varc,
			'time' => microtime(true)
		);

		return $this;
	}

	/**
	 * Get a list of messages
	 *
	 * @return  array
	 */
	public function messages()
	{
		$messages = $this->_messages;

		// sort messages by their timestamp
		usort($messages, function($a, $b)
		{
			if ($a['time'] === $b['time'])
			{
				return 0;
			}
			return $a['time'] < $b['time'] ? -1 : 1;
		});

		return $messages;
	}

	/**
	 * Deletes all messages
	 *
	 * @return  void
	 */
	public function clear()
	{
		$this->_messages = array();
	}

	/**
	 * Get a count and list of messages
	 *
	 * @return  array
	 */
	public function collect()
	{
		$messages = $this->messages();

		return array(
			'count'    => count($messages),
			'messages' => $messages
		);
	}

	/**
	 * Does the log have any messages?
	 *
	 * @return  integer
	 */
	public function hasMessages()
	{
		return count($this->messages());
	}

	/**
	 * Render
	 *
	 * @param   object  $renderer
	 * @return  string
	 */
	public function render($renderer=null)
	{
		if (!$renderer)
		{
			$renderer = php_sapi_name() === 'cli' ? 'console' : 'html';
		}

		return $this->setRenderer($renderer)
					->getRenderer()
					->render($this->messages());
	}

	/**
	 * Adds a message
	 *
	 * @param   mixed  $var
	 * @return  void
	 */
	public static function log($var)
	{
		$console = self::getInstance();
		$console->addVar($var);
	}

	/**
	 * Dumps a var
	 *
	 * @param   mixed   $var
	 * @param   string  $to
	 * @return  void
	 */
	public static function dump($var, $to=null)
	{
		$console = self::getInstance();
		$console->addVar($var);

		echo $console->render($to);
		$console->clear();
	}

	/**
	 * Dumps a var and dies();
	 *
	 * @param   mixed   $var
	 * @param   string  $to
	 * @return  void
	 */
	public static function stop($var, $to=null)
	{
		self::dump($var, $to);
		die();
	}
}
