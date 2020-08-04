<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption;

/**
 * Encryption key object
 *
 * Inspired by Joomla's JCryptKey class
 */
class Key
{
	/**
	 * The private key.
	 *
	 * @var  string
	 */
	public $private;

	/**
	 * The public key.
	 *
	 * @var  string
	 */
	public $public;

	/**
	 * The key type.
	 *
	 * @var  string
	 */
	public $type;

	/**
	 * Constructor.
	 *
	 * @param   string  $type     The key type.
	 * @param   string  $private  The private key.
	 * @param   string  $public   The public key.
	 * @return  void
	 */
	public function __construct($type, $private = null, $public = null)
	{
		// Set the key type.
		$this->type = (string) $type;

		// Set the optional public/private key strings.
		$this->private = isset($private) ? (string) $private : null;
		$this->public  = isset($public)  ? (string) $public  : null;
	}
}
