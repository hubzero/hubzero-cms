<?php

namespace Plugins\Content\Spamassassin\Service\Client;

use Plugins\Content\Spamassassin\Service\Client\Remote\Exception;
use Plugins\Content\Spamassassin\Service\Client\Remote\Result;

require_once __DIR__ . '/Remote/Exception.php';
require_once __DIR__ . '/Remote/Result.php';

/**
 * Client for accessing a remote SpamAssassin service
 */
class Remote
{
	protected $server  = 'http://spamcheck.postmarkapp.com/filter';
	protected $verbose = false;

	/**
	 * Class constructor
	 *
	 * Accepts an associative array with the following keys:
	 *
	 * server     - mandatory only if using remote SpamAssassin server
	 * verbose    - optional parameter
	 *
	 * @param array $params SpamAssassin parameters
	 */
	public function __construct(array $params)
	{
		foreach ($params as $param => $value)
		{
			$this->$param = $value;
		}
	}

	/**
	 * Parses SpamAssassin output ($header and $message)
	 *
	 * @param  string $header  Output headers
	 * @param  string $message Output message
	 * @return Result Object containing the result
	 */
	protected function parseOutput($report, $message)
	{
		$result = new Result();

		$result->output = json_encode($report);

		if (preg_match(
			'/Spam: (True|False|Yes|No) ; (\S+) \/ (\S+)/',
			$report->message,
			$matches
		))
		{
			($matches[1] == 'True' || $matches[1] == 'Yes') ?
				$result->isSpam = true :
				$result->isSpam = false;

			$result->score    = (float) $matches[2];
			$result->thresold = (float) $matches[3];
		}
		else
		{
			/**
			 * In PROCESS method with protocol version before 1.3, SpamAssassin
			 * won't return the 'Spam:' field in the response header. In this case,
			 * it is necessary to check for the X-Spam-Status: header in the
			 * processed message headers.
			*/
			if (preg_match(
				  '/X-Spam-Status: (Yes|No)\, score=(\d+\.\d) required=(\d+\.\d)/',
				  $report->message,
				  $matches))
			{

				($matches[1] == 'Yes') ?
					$result->isSpam = true :
					$result->isSpam = false;

				$result->score    = (float) $matches[2];
				$result->thresold = (float) $matches[3];
			}
		}

		$result->report  = $report->message;
		$result->message = $message;

		return $result;
	}

	/**
	 * Returns a detailed report if the message is spam or null if it's ham
	 *
	 * @param  string $message Email message
	 * @return string Detailed spam report
	 */
	public function getSpamReport($message)
	{
		return $this->check($message)->report;
	}

	/**
	 * Checks if a message is spam with the CHECK protocol command
	 *
	 * @param  string $message Raw email message
	 * @return Result Object containing the result
	 */
	public function check($message)
	{
		$curl_headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
		);

		$json = array(
			'options' => ($this->verbose ? 'long' : 'short'),
			'email'   => $message,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->server);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);

		$result = curl_exec($ch);
		$error  = curl_error($ch);
		$code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if (!$result)
		{
			throw new Exception('cURL Error: ' . $error);
		}

		$result = json_decode($result);
		if (!isset($result->success) || !$result->success)
		{
			throw new Exception(
				'Postmark Error: ' . (isset($result->message) && $result->message ? $result->message : 'unknown')
			);
		}

		//return $result;
		return $this->parseOutput($result, $message);
	}

	/**
	 * Shortcut to check() method that returns a boolean
	 *
	 * @param  string $message Raw email message
	 * @return boolean Whether message is spam or not
	 */
	public function isSpam($message)
	{
		return $this->check($message)->isSpam;
	}

	/**
	 * Shortcut to check() method that returns a float score
	 *
	 * @param  string $message Raw email message
	 * @return float Spam Score of the Message
	 */
	public function getScore($message)
	{
		return $this->check($message)->score;
	}
}
