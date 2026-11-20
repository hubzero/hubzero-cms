<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class ErrorMessageHelper
{
    /** @var string */
    protected $breaks;

    public function __construct()
    {
        $this->breaks = '<br/><br/>';
    }

    /**
     * @param   array<int, mixed>  $errors
     * @return  string
     */
    public function generateErrorMessage($errors)
    {
        $errorList = implode($this->breaks . '• ', $errors);

        $errorMessage = "• $errorList";

        return rtrim($errorMessage, $this->breaks);
    }
}
