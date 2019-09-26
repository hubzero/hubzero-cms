<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class ErrorMessageHelper
{

	protected $breaks;

	public function __construct()
	{
		$this->breaks = '<br/><br/>';
	}

	public function generateErrorMessage($errors)
	{
		$errorList = implode($this->breaks . '• ', $errors);

		$errorMessage = "• $errorList";

		return rtrim($errorMessage, $this->breaks);
	}

}
