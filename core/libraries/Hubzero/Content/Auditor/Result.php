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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Auditor;

use Hubzero\Database\Relational;

/**
 * Test result
 */
class Result extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'audit';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'processed';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'scope'    => 'notempty',
		'scope_id' => 'positive|nonzero',
		'test_id'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $always = array(
		'processed'
	);

	/**
	 * Set processed timestamp
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticProcessed($data)
	{
		$dt = new \Hubzero\Utility\Date();
		return $dt->toSql();
	}

	/**
	 * Did the test pass?
	 *
	 * @return  bool
	 */
	public function passed()
	{
		return ($this->get('status') == 1);
	}

	/**
	 * Did the test fail?
	 *
	 * @return  bool
	 */
	public function failed()
	{
		return ($this->get('status') == -1);
	}

	/**
	 * Was the test skipped?
	 *
	 * @return  bool
	 */
	public function skipped()
	{
		return ($this->get('status') == 0);
	}

	/**
	 * Transform answer
	 *
	 * @return  string
	 */
	public function transformStatus()
	{
		if ($this->skipped())
		{
			return 'skipped';
		}

		if ($this->passed())
		{
			return 'passed';
		}

		if ($this->failed())
		{
			return 'failed';
		}

		return $this->get('status');
	}
}
