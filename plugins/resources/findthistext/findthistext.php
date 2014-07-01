<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Display sponsors on a resource page
 */
class plgResourcesFindThisText extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_findthistext', 0)
			&& $model->access('view-all'))
		{
			$areas['findthistext'] = JText::_('PLG_RESOURCES_FINDTHISTEXT');
		}

		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
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
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'index'
				)
			);
			$view->option   = $option;
			$view->model    = $model;
			$view->database = JFactory::getDBO();
			$view->juser    = JFactory::getUser();
			$view->plugin   = $this->params;
			$view->openurl  = $this->getOpenUrl();

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Get Open URL
	 *
	 * @return     string
	 */
	private function getOpenUrl()
	{
		//var to store open url stuff
		$openUrl = null;

		//get the users id to make lookup
		$userIp = JRequest::ip();

		//get the param for ip regex to use machine ip
		$ipRegex = array(
			'10.\d{2,5}.\d{2,5}.\d{2,5}',
			'192.\d{1,5}.\d{1,5}.\d{1,5}'
		);

		// do we use the machine ip?
		$useMachineIp = false;
		foreach ($ipRegex as $ipr)
		{
			$match = preg_match('/' . $ipr . '/i', $userIp);
			if ($match)
			{
				$useMachineIp = true;
			}
		}

		//make url based on if were using machine ip or users
		if ($useMachineIp)
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $_SERVER['SERVER_ADDR'];
		}
		else
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $userIp;
		}

		//get the resolver
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

		//parse the return from resolver lookup
		$xml = simplexml_load_string($r);
		$resolver = $xml->resolverRegistryEntry->resolver;

		//if we have resolver set vars for creating open urls
		if ($resolver != null)
		{
			$openUrl = new stdClass;
			$openUrl->link = $resolver->baseURL;
			$openUrl->text = $resolver->linkText;
			$openUrl->icon = $resolver->linkIcon;
		}

		// return open url
		return $openUrl;
	}
}