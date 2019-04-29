<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for providing data to OAI-PMH.
 */
class plgOaipmhPublications extends \Hubzero\Plugin\Plugin
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
