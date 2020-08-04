<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Detector;

use Hubzero\Base\Obj;

/**
 * Abstract spam detector service
 */
abstract class Service extends Obj implements DetectorInterface
{
	/**
	 * Message to report
	 *
	 * @var  string
	 */
	protected $message = '';

	/**
	 * The value to be validated
	 *
	 * @var mixed
	 */
	protected $_value;

	/**
	 * Returns the validation value
	 *
	 * @return  mixed  Value to be validated
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * Sets the value to be validated and clears the errors arrays
	 *
	 * @param   mixed  $value
	 * @return  void
	 */
	public function setValue($value)
	{
		$this->_value  = $value;
		$this->_errors = array();
		$this->message = '';
	}

	/**
	 * Run content through spam detection
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		return false;
	}

	/**
	 * Train the service
	 *
	 * @param   string   $data
	 * @param   boolean  $isSpam
	 * @return  boolean
	 */
	public function learn($data, $isSpam)
	{
		if (!$data)
		{
			return false;
		}

		return true;
	}

	/**
	 * Forget a trained value
	 *
	 * @param   string   $data
	 * @param   boolean  $isSpam
	 * @return  boolean
	 */
	public function forget($data, $isSpam)
	{
		return true;
	}

	/**
	 * Return any message the service may have
	 *
	 * @return  string
	 */
	public function message()
	{
		return $this->message;
	}
}
