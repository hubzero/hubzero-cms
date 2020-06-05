<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Handles asynchrous enqueuement, helps maintain the index
 */
require_once Component::path('com_search') . DS . 'helpers' . DS . 'discoveryhelper.php';
require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

use Components\Search\Helpers\DiscoveryHelper;
use Components\Search\Models\Solr\SearchComponent;
use Components\Search\Models\Solr\Blacklist;
use GuzzleHttp\Client;

class plgSearchRemote extends \Hubzero\Plugin\Plugin
{
	/**
	 * onContentSave 
	 * 
	 * @param   mixed  $table
	 * @param   mixed  $model
	 * @return  void
	 */
	public function sendSolrRequest($results, $method)
	{
			try
			{
				$results = !is_array($results) ? array($results) : $results;
				foreach ($results as &$result)
				{
					$result = SearchComponent::addDomainNameSpace($result);
				}
				unset($result);
				$params = Plugin::params('search', 'remote');
				$url = $params->get('app_url');
				$token = $params->get('app_token');
				$client = new \GuzzleHttp\Client(['verify' => false]);
				$response = $client->request('POST', $url, [
					'form_params' => [
						'access_token' => $token,
						'result' => json_encode($results),
						'method' => $method
					]
				]);
				$body = $response->getBody();
			}
			catch (Exception $e)
			{

			}
	}

	/**
	 * Triggers when the plugin is marked as diabled 
	 * 
	 * @param   string  $extension 	the type of extension
	 * @param   Components\Plugins\Models\Plugin 	$model	the model of the plugin being deactivated
	 * @return  void
	 */
	public function onExtensionAfterDelete($extension, Components\Plugins\Models\Plugin $model)
	{
		$className = strtolower(preg_replace('/([A-Z])/', '_$1', get_class($this)));
		if ($model->name === $className)
		{
			$params = Plugin::params('search', 'remote');
			$url = $params->get('app_url');
			$token = $params->get('app_token');
			if (!empty($url) && !empty($token))
			{
				$result = new stdClass;
				$this->sendSolrRequest($result, 'delete');
			}
		}
	}
}
