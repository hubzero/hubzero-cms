<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Opensearch;

use Hubzero\Base\Obj;

/**
 * URL for the OpenSearch Description
 *
 * Inspired by Joomla's JOpenSearchUrl class
 */
class Url extends Obj
{
	/**
	 * Type item element
	 *
	 * @var  string
	 */
	public $type = 'text/html';

	/**
	 * Rel item element
	 *
	 * @var  string
	 */
	public $rel = 'results';

	/**
	 * Template item element.
	 * Has to contain the {searchTerms} parameter to work.
	 *
	 * @var  string
	 */
	public $template;
}
