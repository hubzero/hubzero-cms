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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Resources Plugin class for showing social sharing options
 */
class plgResourcesShare extends \Hubzero\Plugin\Plugin
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
		static $area = array();

		if (!$model->type->params->get('plg_share'))
		{
			return $area;
		}

		return $area;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		if (!$model->type->params->get('plg_share'))
		{
			return;
		}

		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		$juri = JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $option . '&id=' . $model->resource->id);
		$url = $juri->base() . ltrim($sef, DS);

		// Incoming action
		$sharewith = JRequest::getVar('sharewith', '');
		if ($sharewith && $sharewith != 'email')
		{
			$this->share($sharewith, $url, $model->resource);
			return;
		}

		// Email form
		if ($sharewith == 'email')
		{
			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'options',
					'layout'  => 'email'
				)
			);

			// Pass the view some info
			$view->option   = $option;
			$view->resource = $model->resource;
			$view->_params  = $this->params;
			$view->url      = $url;
			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			// Return the output
			$view->display();
			exit();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'options'
				)
			);

			// Pass the view some info
			$view->option   = $option;
			$view->resource = $model->resource;
			$view->_params  = $this->params;
			$view->url      = $url;
			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			// Return the output
			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Redirect to social sharer
	 *
	 * @param      string $with     Social site to share with
	 * @param      string $url      The URL to share
	 * @param      object $resource Resource to share
	 * @return     void
	 */
	public function share($with, $url, $resource)
	{
		$jconfig = JFactory::getConfig();

		$link = '';
		switch ($with)
		{
			case 'facebook':
				$link = 'http://www.facebook.com/sharer.php?u=' . $url;
			break;

			case 'twitter':
				$link = 'http://twitter.com/home?status=' . JText::sprintf('PLG_RESOURCES_SHARE_VIEWING', $jconfig->getValue('config.sitename'), stripslashes($resource->title));
			break;

			case 'google':
				$link = 'https://plus.google.com/share?url=' . $url . '&title=' . $jconfig->getValue('config.sitename') . ': ' . JText::_('PLG_RESOURCES_SHARE_RESOURCE') . ' ' . $resource->id . ' - ' . stripslashes($resource->title) . '&labels=' . $jconfig->getValue('config.sitename');
			break;

			case 'digg':
				$link = 'http://digg.com/submit?phase=2&url=' . $url . '&title=' . $jconfig->getValue('config.sitename') . ': ' . JText::_('PLG_RESOURCES_SHARE_RESOURCE') . ' ' . $resource->id . ' - ' . stripslashes($resource->title);
			break;

			case 'technorati':
				$link = 'http://www.technorati.com/faves?add='.$url;
			break;

			case 'delicious':
				$link = 'http://del.icio.us/post?url=' . $url . '&title=' . $jconfig->getValue('config.sitename') . ': ' . JText::_('PLG_RESOURCES_SHARE_RESOURCE') . ' ' . $resource->id . ' - ' . stripslashes($resource->title);
			break;

			case 'reddit':
				$link = 'http://reddit.com/submit?url=' . $url . '&title=' . $jconfig->getValue('config.sitename') . ': ' . JText::_('PLG_RESOURCES_SHARE_RESOURCE') . ' ' . $resource->id . ' - ' . stripslashes($resource->title);
			break;
		}

		if ($link)
		{
			$app = JFactory::getApplication();
			$app->redirect($link, '', '');
		}
	}
}

