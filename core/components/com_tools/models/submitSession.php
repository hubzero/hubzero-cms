<?php
/*
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

$componentPath = Component::path('com_tools');

require_once "$componentPath/traits/modifiable.php";

use Hubzero\Database\Relational;
use Components\Tools\Traits\modifiable;

class SubmitSession extends Relational
{
	// intelligently updates modified field
	use modifiable;

	/*
	 * Integer to status mapping
	 *
	 * @var array
	 */
	public static $statusMap = [
		0 => 'aborted',
		1 => 'executing',
		2 => 'failed',
		3 => 'finished',
		4 => 'setting_up',
		5 => 'setup',
		6 => 'waiting'
	];

	/*
	 * Records table
	 *
	 * @var string
	 */
	protected $table = '#__tool_submit_sessions';

	/*
	 * Attributes to be populated on record creation
	 *
	 * @var array
	 */
	public $initiate = ['created'];

	/*
	 * Attributes to update whenever a record is saved
	 *
	 * @var array
	 */
	public $always = ['modified'];

	/*
	 * Retrieves record by deeds session or returns new instance
	 *
	 * @param    int   $deedsSession   Session deeds ID
	 * @return   Hubzero\Relational
	 */
	public static function oneByDeedsOrNew($deedsSession)
	{
		$queryResults = self::blank()
			->whereEquals('deeds_session', $deedsSession)
			->rows()
			->raw();

		if (empty($queryResults))
		{
			$submitSession = self::blank();
		}
		else
		{
			$submitSession = array_shift($queryResults);
		}

		return $submitSession;
	}

	/*
	 * Getter for model validation rules
	 *
	 * @return   array
	 */
	public function getRules()
	{
		$rules = [
			'submit_status' => function($submitStatus) {
					return $this->_submitStatusValidator($submitStatus);
			}
		];

		return $rules;
	}

	/*
	 * Submit status validator function
	 *
	 * @param    mixed   $attributes   Submit session attributes
	 * @return   mixed
	 */
	protected function _submitStatusValidator($attributes)
	{
		$submitStatus = $attributes['submit_status'];

		if (isset(self::$statusMap[$submitStatus]))
		{
			$result = false;

			// translate numeric status to name
			$namedStatus = self::$statusMap[$this->get('submit_status')];
			$this->set('submit_status', $namedStatus);
		}
		else
		{
			$statusMappingMessage = "Status map: ";

			foreach (self::$statusMap as $key => $name)
			{
				$statusMappingMessage .= "$key => $name, ";
			}

			$statusMappingMessage = rtrim($statusMappingMessage, ', ');

			$result = "Submit status $submitStatus not found. $statusMappingMessage.";
		}

		return $result;
	}

}
