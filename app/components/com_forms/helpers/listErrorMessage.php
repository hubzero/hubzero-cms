<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use Hubzero\Utility\Arr;

class ListErrorMessage
{

	/**
	 * Constructs ListErrorMessage instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_errorIntro = Arr::getValue($args, 'errorIntro', '');
		$this->_errors = $args['errors'];
	}

	/**
	 * Returns a string representation of $this
	 *
	 * @param    string   $errorIntro   Top level error text
	 * @return   string
	 */
	public function toString($errorIntro = '')
	{
		$errorIntro = $errorIntro ? $errorIntro : $this->_errorIntro;
		$errorMessage = "$this->_errorIntro <br/>";

		foreach ($this->_errors as $key => $error)
		{
			$errorMessage .= $this->_parseError($key, $error);
		}

		$errorMessage = "$errorMessage<br/><br/>";

		return $errorMessage;
	}

	/**
	 * Handles preparing errors for
	 *
	 * @param    string   $key     Key of error(s) in records errors array
	 * @param    mixed    $error   Records error(s)
	 * @return   string
	 */
	protected function _parseError($key, $error)
	{
		if (is_array($error))
		{
			$error = $this->_combineErrors($key, $error);
		}
		else
		{
			$error = $this->_formatError($error);
		}

		return $error;
	}

	/**
	 * Combines the errors of a record into a single message
	 *
	 * @param    string   $topLevelError   Top level erroor message
	 * @param    array    $errors          Records error descriptions
	 * @return   string
	 */
	protected function _combineErrors($topLevelError, $errors)
	{
		$errorMessage = $this->_formatError($topLevelError);

		foreach ($errors as $error)
		{
				$errorMessage .= "<br/>&nbsp;&nbsp;&nbsp; - $error";
		}

		return $errorMessage;
	}

	/**
	 * Formats error for addition to top level of message
	 *
	 * @param    string   $error   Error text
	 * @return   string
	 */
	protected function _formatError($error)
	{
		return "<br/>• $error";
	}

}
