<?php

namespace Plugins\Oaipmh\Publications;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @param   \Components\Oaipmh\Models\Service  $service
     * @return  void
     */
    public function onOaipmhProvider(&$service)
    {

        $provider = new \Plugins\Oaipmh\Publications\Data\Miner();
        $provider->set('type', $this->params->get('type'));

        $service->register($provider->name(), $provider);
    }
}
