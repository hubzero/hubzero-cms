<?php
/**
 * CTCException class
 */
class CTCTException extends Exception
{
	public function __construct($message, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code);
	}

	public function generateError($msgPrefix=null)
	{
		$this->logError($this->message);
		echo $msgPrefix.' '.$this->getMessage().'<br />';
	}

	private function logError($errorText, $file="error.log")
	{
		date_default_timezone_set('America/New_York');
		$message = "Constant Contact Exception -- ".date("F j, Y, g:i:sa")."\n".$errorText."\n";
		$message .= "Stack Trace: ".$this->getTraceAsString()."\n";
		error_log($message."\n", 3, $file);
	}
}
