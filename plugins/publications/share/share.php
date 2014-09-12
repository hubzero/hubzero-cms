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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Short description for 'plgPublicationsShare'
 * 
 * Long description (if any) ...
 */
class plgPublicationsShare extends JPlugin
{

	/**
	 * Short description for 'plgPublicationsShare'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgPublicationsShare(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'publications', 'share' );
		$this->_params = new JParameter( $this->_plugin->params );

		$this->loadLanguage();
	}

	/**
	 * Short description for 'onPublicationsAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $publication Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &onPublicationAreas( $publication, $version = 'default', $extended = true )
	{
		$areas = array();
		return $areas;
	}

	/**
	 * Short description for 'onPublications'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $publication Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @param      string $rtrn Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onPublication( $publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Hide if version not published
		if ($publication->state == 4 || $publication->state == 5 || $publication->state == 6) 
		{
			return $arr;
		}

		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$option.'&id='.$publication->id);
		if (substr($sef,0,1) == '/') 
		{
			$sef = substr($sef,1,strlen($sef));
		}
		$url = $juri->base().$sef;
		$url = $url . DS . '?v=' . $publication->version_number;

		// Incoming action
		$sharewith = JRequest::getVar('sharewith', '');
		if ($sharewith && $sharewith != 'email') {
			$this->share($sharewith, $url, $publication, $version);
			return;
		}

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('publications', 'share');
		Hubzero_Document::addPluginScript('publications', 'share');

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'publications',
					'element'=>'share',
					'name'=>'options'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->publication = $publication;
			$view->version = $version;
			$view->_params = $this->_params;
			$view->url = $url;
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Short description for 'share'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $with Parameter description (if any) ...
	 * @param      string $url Parameter description (if any) ...
	 * @param      mixed $publication Parameter description (if any) ...
	 * @return     void
	 */
	public function share($with, $url, $publication, $version)
	{
		$jconfig =& JFactory::getConfig();

		$link = '';
		switch ($with)
		{
			case 'facebook':
				$link = 'http://www.facebook.com/sharer.php?u='.$url;
				break;

			case 'twitter':
				$link = 'http://twitter.com/home?status=' . urlencode(JText::sprintf('PLG_PUBLICATION_SHARE_VIEWING', 
						$jconfig->getValue('config.sitename'), 
						stripslashes($publication->title)).' '.$url);
				break;

			case 'google':
				$link = 'http://www.google.com/bookmarks/mark?op=edit&bkmk='.$url
					.'&title='.$jconfig->getValue('config.sitename').': '
					.JText::_('PLG_PUBLICATION_SHARE_RESOURCE').' '.$publication->id
					.' - '.stripslashes($publication->title).'&labels='.$jconfig->getValue('config.sitename');
				break;

			case 'digg':
				$link = 'http://digg.com/submit?phase=2&url='.$url
				.'&title='.$jconfig->getValue('config.sitename').': '
				.JText::_('PLG_PUBLICATION_SHARE_RESOURCE').' '.$publication->id
				.' - '.stripslashes($publication->title);
				break;

			case 'technorati':
				$link = 'http://www.technorati.com/faves?add='.$url;
				break;

			case 'delicious':
				$link = 'http://del.icio.us/post?url='.$url.'&title='
				.$jconfig->getValue('config.sitename').': '.JText::_('PLG_PUBLICATION_SHARE_RESOURCE')
				.' '.$publication->id.' - '.stripslashes($publication->title);
				break;

			case 'reddit':
				$link = 'http://reddit.com/submit?url='.$url.'&title='
				.$jconfig->getValue('config.sitename').': '.JText::_('PLG_PUBLICATION_SHARE_RESOURCE')
				.' '.$publication->id.' - '.stripslashes($publication->title);
				break;
		}
		
		if ($link) 
		{
			$app =& JFactory::getApplication();
			$app->redirect($link, '', '');
		}
	}
}

