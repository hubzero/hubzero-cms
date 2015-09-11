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
 * Item is an internal class that stores feed item information
 *
 * Inspired by Joomla's JFeedItem class
 */
class Item extends Object
{
	/**
	 * Title item element
	 *
	 * @var  string
	 */
	public $title;

	/**
	 * Link item element
	 *
	 * @var  string
	 */
	public $link;

	/**
	 * Description item element
	 *
	 * @var  string
	 */
	public $description;

	/**
	 * Author item element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $author;

	/**
	 * Author email element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $authorEmail;

	/**
	 * Category element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $category;

	/**
	 * Comments element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $comments;

	/**
	 * Enclosure element
	 *
	 * @var	 object
	 */
	public $enclosure =  null;

	/**
	 * Guid element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $guid;

	/**
	 * Published date
	 *
	 * optional
	 *
	 *  May be in one of the following formats:
	 *
	 *	RFC 822:
	 *	"Mon, 20 Jan 03 18:05:41 +0400"
	 *	"20 Jan 03 18:05:41 +0000"
	 *
	 *	ISO 8601:
	 *	"2003-01-20T18:05:41+04:00"
	 *
	 *	Unix:
	 *	1043082341
	 *
	 * @var	 string
	 */
	public $pubDate;

	/**
	 * Source element
	 *
	 * optional
	 *
	 * @var	 string
	 */
	public $source;

	/**
	 * iTunes summary
	 *
	 * @var  string
	 */
	public $itunes_summary = '';

	/**
	 * iTunes 'explicit content' flag
	 *
	 * @var  string
	 */
	public $itunes_explicit = "no";

	/**
	 * iTunes keywords
	 *
	 * @var  string
	 */
	public $itunes_keywords = '';

	/**
	 * iTunes author
	 *
	 * @var  string
	 */
	public $itunes_author = '';

	/**
	 * iTunes image
	 *
	 * @var  string
	 */
	public $itunes_image = '';

	/**
	 * iTunes duration (video, sound)
	 *
	 * @var  string
	 */
	public $itunes_duration = '';

	/**
	 * iTunes category
	 *
	 * @var  string
	 */
	public $itunes_category = '';

	/**
	 * iTunes subcategories
	 *
	 * @var  string
	 */
	public $itunes_subcategories = null;

	/**
	 * Set the Enclosure for this item
	 *
	 * @param   object  $enclosure  The Item to add to the feed.
	 * @return  void
	*/
	public function setEnclosure($enclosure)
	{
		$this->enclosure = $enclosure;
	}
}

