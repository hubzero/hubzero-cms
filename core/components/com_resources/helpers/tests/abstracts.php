<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers\Tests;

use Hubzero\Content\Auditor\Test;
use Hubzero\Content\Auditor\Result;

/**
 * Checker
 */
class Abstracts implements Test
{
	/**
	 * Register a test
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'Abstract Checker';
	}

	/**
	 * Checks if a string is spam or not
	 *
	 * @param   array  $data
	 * @param   array  $options
	 * @return  object
	 */
	public function examine(array $data, array $options = array())
	{
		$status = 0;
		$value  = null;
		$meta   = array();

		if (isset($data['standalone']) && $data['standalone'])
		{
			$status = 1;

			$meta['field'] = isset($data['title']) ? $data['title'] : $data['id'];

			if (isset($data['introtext']))
			{
				$value = $data['introtext'];
			}

			if (isset($data['fulltxt']))
			{
				$value = $data['fulltxt'];
			}

			if (!$value)
			{
				$status = -1;
			}
		}

		$result = new Result();
		$result->set([
			'scope_id' => $data['id'],
			'status'   => $status,
			'notes'    => json_encode($meta)
		]);

		return $result;
	}
}
