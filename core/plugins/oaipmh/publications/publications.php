<?php

namespace Plugins\Oaipmh\Publications;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Plugin class for providing data to OAI-PMH.
 *
 */
class Publications extends Plugin
{
    /**
     * Instantiate and registers a provider with the
     * OAI-PMH service
     *
     * @param   object  $service
     * @return  void
     */
    public function onOaipmhProvider(&$service)
    {
        require_once __DIR__ . DS . 'data' . DS . 'miner.php';

        $provider = new \Plugins\Oaipmh\Publications\Data\Miner();
        $provider->set('type', $this->params->get('type'));

        $service->register($provider->name(), $provider);
    }
}
