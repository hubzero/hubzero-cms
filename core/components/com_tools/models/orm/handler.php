<?php
/**
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'rule.php';
require_once __DIR__ . DS . 'tool.php';

/**
 * Tool file handlers database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Handler extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'tool';

	/**
	 * Default order dir for fetch
	 *
	 * @var string
	 **/
	public $orderBy = 'tool.title';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'tool_id' => 'notempty|nonzero',
		'prompt'  => 'notempty'
	);

	/**
	 * Defines the relationship between a handler and it's rules
	 *
	 * @return \Hubzero\Database\Relationship\oneToMany
	 **/
	public function rules()
	{
		return $this->oneToMany('Rule');
	}

	/**
	 * Defines the inverse relationship between a handler and a tool
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 **/
	public function tool()
	{
		return $this->belongsToOne('Tool');
	}

	/**
	 * Returns the known handlers for a given file
	 *
	 * @return  array|bool
	 **/
	public static function getLaunchUrlsForFile($filename)
	{
		// Figure out the extension of interest
		$bits      = explode('.', $filename);
		$extension = array_pop($bits);
		$results   = [];

		// First, limit handlers to only those with 1 rule
		$handlers = static::all()->whereRelatedHasCount('rules', 1, 0, '=');

		// Now, make sure that rule is for this file extension and only has a quantity of 1
		$rows = $handlers->whereRelatedHas('rules', function ($rules) use ($extension)
		{
			$rules->whereEquals('extension', $extension)
			      ->whereEquals('quantity', 1);
		})->rows();

		if ($rows->count() == 0)
		{
			return false;
		}
		else
		{
			foreach ($rows as $row)
			{
				$results[] = [
					'prompt' => $row->prompt,
					'url'    => '/tools/' . $row->tool->toolname . '/invoke?params=file:' . urlencode($filename)
				];
			}
		}

		return $results;
	}
}
