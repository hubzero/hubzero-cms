<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pagination;

use Hubzero\Base\Obj;

/**
 * Pagination object representing a particular item in the pagination lists.
 */
class Item extends Obj
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
	 * @var  string
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
