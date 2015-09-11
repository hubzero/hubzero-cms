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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pagination;

use Hubzero\Base\Object;

/**
 * Pagination object representing a particular item in the pagination lists.
 */
class Item extends Object
{
	/**
	 * The link text.
	 *
	 * @var  string
	 */
	public $text;

	/**
	 * The number of rows as a base offset.
	 *
	 * @var  integer
	 */
	public $base;

	/**
	 * The link URL.
	 *
	 * @var  string
	 */
	public $link;

	/**
	 * The prefix used for request variables.
	 *
	 * @var  integer
	 */
	public $prefix;

	/**
	 * The prefix used for request variables.
	 *
	 * @var  integer
	 */
	public $rel;

	/**
	 * Class constructor.
	 *
	 * @param   string   $text    The link text.
	 * @param   integer  $prefix  The prefix used for request variables.
	 * @param   integer  $base    The number of rows as a base offset.
	 * @param   string   $link    The link URL.
	 * @return  void
	 */
	public function __construct($text, $prefix = '', $base = null, $link = null)
	{
		$this->text   = $text;
		$this->prefix = $prefix;
		$this->base   = $base;
		$this->link   = $link;
	}
}
