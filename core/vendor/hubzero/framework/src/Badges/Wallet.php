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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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