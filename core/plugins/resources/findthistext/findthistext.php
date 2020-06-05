<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display sponsors on a resource page
 */
class plgResourcesFindThisText extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $model  Current model
	 * @return  array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_findthistext', 0)
			&& $model->access('view-all'))
		{
			$areas['findthistext'] = Lang::txt('PLG_RESOURCES_FINDTHISTEXT');
		}

		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object  $model   Current model
	 * @param   string  $option  Name of the component
	 * @param   array   $areas   Active area(s)
	 * @param   string  $rtrn    Data to be returned
	 * @return  array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model))))
			{
				$rtrn = '';
			}
		}

		if ($rtrn == 'all' || $rtrn == 'html')
		{
			// Instantiate a view
			$view = $this->view('default', 'index')
				->set('option', $option)
				->set('model', $model)
				->set('plugin', $this->params)
				->set('openurl', $this->getOpenUrl());

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Get Open URL
	 *
	 * @return  string
	 */
	private function getOpenUrl()
	{
		// Var to store open url stuff
		$openUrl = null;

		// Get the users id to make lookup
		$userIp = Request::ip();

		// Get the param for ip regex to use machine ip
		$ipRegex = array(
			'10.\d{2,5}.\d{2,5}.\d{2,5}',
			'192.\d{1,5}.\d{1,5}.\d{1,5}'
		);

		// Do we use the machine IP?
		$useMachineIp = false;
		foreach ($ipRegex as $ipr)
		{
			$match = preg_match('/' . $ipr . '/i', $userIp);
			if ($match)
			{
				$useMachineIp = true;
			}
		}

		// Make URL based on if were using machine ip or users
		if ($useMachineIp)
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $_SERVER['SERVER_ADDR'];
		}
		else
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $userIp;
		}

		// Get the resolver
		$r = null;
		if (function_exists('curl_init'))
		{
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $url );
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cURL, CURLOPT_TIMEOUT, 10);
			$r = curl_exec($cURL);
			curl_close($cURL);
		}

		// Parse the return from resolver lookup
		$xml = simplexml_load_string($r);
		$resolver = $xml->resolverRegistryEntry->resolver;

		// If we have resolver set vars for creating open urls
		if ($resolver != null)
		{
			$openUrl = new stdClass;
			$openUrl->link = $resolver->baseURL;
			$openUrl->text = $resolver->linkText;
			$openUrl->icon = $resolver->linkIcon;
		}

		// Return open URL
		return $openUrl;
	}
}
