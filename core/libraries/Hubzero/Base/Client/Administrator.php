<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * Administrator client
 */
class Administrator implements ClientInterface
{
	/**
	 * ID
	 *
	 * @var  integer
	 */
	public $id = 1;

	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'administrator';

	/**
	 * Alias
	 *
	 * @var  string
	 */
	public $alias = 'admin';

	/**
	 * A url to init this client.
	 *
	 * @var  string
	 */
	public $url = 'admin';
}
