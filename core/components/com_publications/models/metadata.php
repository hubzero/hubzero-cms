<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
	 */
	public $title = null;

	/**
	 * Object abstract
	 */
	public $abstract = null;

	/**
	 * Object description
	 */
	public $description = null;

	/**
	 * Object dc type
	 */
	public $type = null;

	/**
	 * URL to resource
	 */
	public $url = null;

	/**
	 * URL to resource
	 */
	public $doi = null;

	/**
	 * Publisher
	 */
	public $publisher = null;

	/**
	 * Journal
	 */
	public $journal = null;

	/**
	 * Subject
	 */
	public $subject = null;

	/**
	 * Language
	 */
	public $language = null;

	/**
	 * Format
	 */
	public $format = null;

	/**
	 * Date
	 */
	public $date = null;

	/**
	 * Date
	 */
	public $issued = null;

	/**
	 * Volume
	 */
	public $volume = null;

	/**
	 * Issue
	 */
	public $issue = null;

	/**
	 * Page
	 */
	public $page = null;

	/**
	 * ISBN
	 */
	public $isbn = null;

	/**
	 * Author
	 */
	public $author = null;
}
