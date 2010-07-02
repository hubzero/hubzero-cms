<?php
/**
 * Error thrown when the user does not have sufficient
 * authorization to perform the requested command.
 *
 * @uses RuntimeException
 */
class UnauthorizedCommandError extends RuntimeException {
  public function __construct($message = "UnauthorizedCommandError", $code=-1) {
    parent::__construct($message,$code);
  }
}
?>