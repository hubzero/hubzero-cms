<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests\Mock;

use Hubzero\Spam\Detector\Service;

/**
 * Mock spam detector
 *
 * @codeCoverageIgnore
 */
class Detector extends Service
{
	/**
	 * Run content through spam detection
	 *
	 * @codeCoverageIgnore
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		if (!is_array($data) || !isset($data['text']))
		{
			return false;
		}

		if (stristr($data['text'], 'spam'))
		{
			$this->message = 'Text contained the word "spam".';
			return true;
		}

		return false;
	}
}
