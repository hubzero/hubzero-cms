<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * Install client
 */
class Install implements ClientInterface
{
	/**
	 * ID
	 *
	 * @var  integer
	 */
	public $id = 3;

	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'install';

	/**
	 * A url to init this client.
	 *
	 * @var  string
	 */
	public $url = 'install';
}
