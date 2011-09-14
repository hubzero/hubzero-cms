<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_share' );

class plgResourcesShare extends JPlugin
{
	public function plgResourcesShare(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'share' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	public function &onResourcesAreas( $resource )
	{
		static $areas = array(
		);

		return $areas;
	}

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$option.'&id='.$resource->id);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$url = $juri->base().$sef;

		// Incoming action
		$sharewith = JRequest::getVar('sharewith', '');
		if ($sharewith && $sharewith != 'email') {
			$this->share($sharewith, $url, $resource);
			return;
		}

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('resources', 'share');

		// Push some scripts to the template
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'resources'.DS.'share'.DS.'share.js')) {
			$document =& JFactory::getDocument();
			$document->addScript('plugins'.DS.'resources'.DS.'share'.DS.'share.js');
		}

		// Email form
		if ($sharewith == 'email') {
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'share',
					'name'=>'options',
					'layout'=>'email'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->resource = $resource;
			$view->_params = $this->_params;
			$view->url = $url;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$view->display();
			exit();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'share',
					'name'=>'options'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->resource = $resource;
			$view->_params = $this->_params;
			$view->url = $url;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	public function share($with, $url, $resource)
	{
		$jconfig =& JFactory::getConfig();

		$link = '';
		switch ($with)
		{
			case 'facebook':
				$link = 'http://www.facebook.com/sharer.php?u='.$url;
			break;

			case 'twitter':
				$link = 'http://twitter.com/home?status='.JText::sprintf('PLG_RESOURCES_SHARE_VIEWING',$jconfig->getValue('config.sitename'),stripslashes($resource->title));
			break;

			case 'google':
				$link = 'http://www.google.com/bookmarks/mark?op=edit&bkmk='.$url.'&title='.$jconfig->getValue('config.sitename').': '.JText::_('PLG_RESOURCES_SHARE_RESOURCE').' '.$resource->id.' - '.stripslashes($resource->title).'&labels='.$jconfig->getValue('config.sitename');
			break;

			case 'digg':
				$link = 'http://digg.com/submit?phase=2&url='.$url.'&title='.$jconfig->getValue('config.sitename').': '.JText::_('PLG_RESOURCES_SHARE_RESOURCE').' '.$resource->id.' - '.stripslashes($resource->title);
			break;

			case 'technorati':
				$link = 'http://www.technorati.com/faves?add='.$url;
			break;

			case 'delicious':
				$link = 'http://del.icio.us/post?url='.$url.'&title='.$jconfig->getValue('config.sitename').': '.JText::_('PLG_RESOURCES_SHARE_RESOURCE').' '.$resource->id.' - '.stripslashes($resource->title);
			break;

			case 'reddit':
				$link = 'http://reddit.com/submit?url='.$url.'&title='.$jconfig->getValue('config.sitename').': '.JText::_('PLG_RESOURCES_SHARE_RESOURCE').' '.$resource->id.' - '.stripslashes($resource->title);
			break;
		}

		if ($link) {
			$app =& JFactory::getApplication();
			$app->redirect($link, '', '');
		}
	}
}

