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

namespace Hubzero\Document\Type\Feed;

use Hubzero\Base\Object;

/**
 * Image is an internal class that stores feed image information
 *
 * Inspired by Joomla's JFeedImage class
 */
class Image extends Object
{
	/**
	 * Title image attribute
	 *
	 * @var	 string
	 */
	public $title = '';

	/**
	 * URL image attribute
	 *
	 * @var	 string
	 */
	public $url = '';

	/**
	 * Link image attribute
	 *
	 * @var	 string
	 */
	public $link = '';

	/**
	 * Image width attribute
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $width;

	/**
	 * Image height attribute
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $height;

	/**
	 * Image description attribute
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $description;
}
