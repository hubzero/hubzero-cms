<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Base\Obj;

/**
 * Image is an internal class that stores feed image information
 *
 * Inspired by Joomla's JFeedImage class
 */
class Image extends Obj
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
