<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Badges;

use Hubzero\Badges\Provider\ProviderInterface;
use Hubzero\Badges\Exception\InvalidProviderException;
use Hubzero\Badges\Exception\ProviderNotFoundException;

/**
 * Hubzero badges class
 */
class Wallet
{
	/**
	 * Badge provider
	 *
	 * @var  object
	 */
	private $_provider;

	/**
	 * Constructor
	 *
	 * @param   string  $provider
	 * @param   string  $requestType
	 * @return  void
	 */
	public function __construct($provider, $requestType='oauth')
	{
		$cls = __NAMESPACE__ . '\\Provider\\' . ucfirst(strtolower($provider));

		if (!class_exists($cls))
		{
			throw new ProviderNotFoundException(\Lang::txt('Invalid badges provider of "%s".', $provider));
		}

		$this->_provider = new $cls($requestType);

		if (!($this->_provider instanceof ProviderInterface))
		{
			throw new InvalidProviderException(\Lang::txt('Invalid badges provider of "%s". Provider must implement ProviderInterface', $provider));
		}
	}

	/**
	 * Get badges provider instance
	 *
	 * @return  object
	 */
	public function getProvider()
	{
		return $this->_provider;
	}
}
