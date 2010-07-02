<?php

/**
 * Error thrown when a $_REQUEST[] parameter is invalid
 *
 * @uses UnexpectedValueException
 */
class InvalidRequestParameterError extends UnexpectedValueException {
  public function __construct($message = "InvalidRequestParameterError", $code=-1) {
    parent::__construct($message,$code);
  }
}
?>