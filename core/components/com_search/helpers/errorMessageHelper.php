<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class ErrorMessageHelper
{

	public function __construct()
	{
		$this->_breaks = '<br/><br/>';
	}

	public function generateErrorMessage($errors)
	{
		$errorList = implode($this->_breaks . '• ', $errors);

		$errorMessage = "• $errorList";

		return rtrim($errorMessage, $this->_breaks);
	}


}
