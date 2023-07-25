<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Traits;

/**
 * Adds entity escaping for string output
 *
 * Methods based on those found in Joomla's JView class.
 */
trait Escapable
{
	/**
	 * Callback for escaping.
	 *
	 * @var  string
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to utf8 (UTF-8)
	 *
	 * @var  string
	 */
	protected $_charset = 'UTF-8';

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is either htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param   mixed  $var  The output to escape.
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if ($var === null)
		{
			return '';
		}

		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
		{
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param   mixed   $spec  The callback for _escape() to use.
	 * @return  object  Chainable
	 */
	public function setEscape($spec)
	{
		$this->_escape = $spec;
		return $this;
	}
}
