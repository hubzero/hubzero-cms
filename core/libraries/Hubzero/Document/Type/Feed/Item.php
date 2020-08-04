<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Base\Obj;

/**
 * Item is an internal class that stores feed item information
 *
 * Inspired by Joomla's JFeedItem class
 */
class Item extends Obj
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
