<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests\Mock;

use Hubzero\Spam\Detector\Service;
use Exception;

/**
 * Mock spam detector that throws an exception
 *
 * @codeCoverageIgnore
 */
class DetectorException extends Service
{
	/**
	 * Run content through spam detection
	 *
	 * @codeCoverageIgnore
	 * @param   array  $data
	 * @return  bool
	 * @throws  Exception
	 */
	public function detect($data)
	{
		if (!is_array($data) || !isset($data['text']))
		{
			return false;
		}

		throw new Exception('I always throw an exception.');
	}
}
