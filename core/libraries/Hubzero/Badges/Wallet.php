<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Badges;

use Hubzero\Badges\Provider\ProviderInterface;
use Hubzero\Badges\Exception\InvalidProviderException;
use Hubzero\Badges\Exception\ProviderNotFoundException;
use Hubzero\Facades\Lang;

/**
 * Hubzero badges class
 */
class Wallet
{
    /**
     * Badge provider
     *
     * @var  ProviderInterface
     */
    private $provider;

    /**
     * Constructor
     *
     * @param   string  $provider
     * @param   string  $requestType
     * @return  void
     */
    public function __construct($provider, $requestType = 'oauth')
    {
        $cls = __NAMESPACE__ . '\\Provider\\' . ucfirst(strtolower($provider));

        if (!class_exists($cls)) {
            throw new ProviderNotFoundException(Lang::txt('Invalid badges provider of "%s".', $provider));
        }

        if (!is_a($cls, ProviderInterface::class, true)) {
            throw new InvalidProviderException(
                Lang::txt(
                    'Invalid badges provider of "%s". Provider must implement ProviderInterface',
                    $provider,
                ),
            );
        }

        $this->provider = new $cls($requestType);
    }

    /**
     * Get badges provider instance
     *
     * @return  ProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
