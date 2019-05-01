<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
