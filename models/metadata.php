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
 * @author    Alissa Nedossekina <aliasa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication DOI metadata base class
 */
class Metadata extends Obj
{
	/**
	 *  Object title
	 *
	 * @var  string
	 */
	public $title = null;

	/**
	 * Object abstract
	 *
	 * @var  string
	 */
	public $abstract = null;

	/**
	 * Object description
	 *
	 * @var  string
	 */
	public $description = null;

	/**
	 * Object dc type
	 *
	 * @var  string
	 */
	public $type = null;

	/**
	 * URL
	 *
	 * @var  string
	 */
	public $url = null;

	/**
	 * DOI
	 *
	 * @var  string
	 */
	public $doi = null;

	/**
	 * Publisher
	 *
	 * @var  string
	 */
	public $publisher = null;

	/**
	 * Journal
	 *
	 * @var  string
	 */
	public $journal = null;

	/**
	 * Subject
	 *
	 * @var  string
	 */
	public $subject = null;

	/**
	 * Language
	 *
	 * @var  string
	 */
	public $language = null;

	/**
	 * Format
	 *
	 * @var  string
	 */
	public $format = null;

	/**
	 * Date
	 *
	 * @var  string
	 */
	public $date = null;

	/**
	 * Date
	 *
	 * @var  string
	 */
	public $issued = null;

	/**
	 * Volume
	 *
	 * @var  string
	 */
	public $volume = null;

	/**
	 * Issue
	 *
	 * @var  string
	 */
	public $issue = null;

	/**
	 * Page
	 *
	 * @var  string
	 */
	public $page = null;

	/**
	 * ISBN
	 *
	 * @var  string
	 */
	public $isbn = null;

	/**
	 * Author
	 *
	 * @var  string
	 */
	public $author = null;
}
