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

/**
 * Short description for 'plgPublicationsShare'
 *
 * Long description (if any) ...
 */
class plgPublicationsShare extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

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
			'metadata'=>'',
			'name'=>'share'
		);

		// Hide if version not published
		if (!$extended || $publication->state == 4 || $publication->state == 5 || $publication->state == 6)
		{
			return $arr;
		}

		$juri = JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$option.'&id='.$publication->id);
		$url = $juri->base() . trim($sef, DS);
		$url = $url . DS . '?v=' . $publication->version_number;

		$mediaUrl = $juri->base() . trim($sef, DS) . DS . $publication->version_id . DS . 'Image:master';

		// Incoming action
		$sharewith = JRequest::getVar('sharewith', '');
		if ($sharewith && $sharewith != 'email')
		{
			$this->share($sharewith, $url, $mediaUrl, $publication, $version);
			return;
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'publications',
					'element'=>'share',
					'name'=>'options'
				)
			);

			// Pass the view some info
			$view->option 		= $option;
			$view->publication 	= $publication;
			$view->version 		= $version;
			$view->_params 		= $this->params;
			$view->url 			= $url;
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
	public function share($with, $url, $mediaUrl, $publication, $version)
	{
		$jconfig = JFactory::getConfig();

		$link = '';
		$description = $publication->abstract
			? \Hubzero\Utility\String::truncate(stripslashes($publication->abstract), 250) : '';
		$description = urlencode($description);
		$title = stripslashes($publication->title);
		$title = urlencode($title);

		switch ($with)
		{
			case 'facebook':
				$link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url . '&t=' . $title;
				break;

			case 'twitter':
				$link = 'http://twitter.com/home?status=' . urlencode(JText::sprintf('PLG_PUBLICATION_SHARE_VIEWING',
						$jconfig->getValue('config.sitename'),
						stripslashes($publication->title) . ' ' . $url));
				break;

			case 'google':
				$link = 'https://plus.google.com/share?url=' . $url;
				break;

			case 'delicious':
				$link = 'http://del.icio.us/post?url=' . $url . '&title=' . $title;
				break;

			case 'reddit':
				$link = 'http://reddit.com/submit?url=' . $url . '&title=' . $title;
				break;

			case 'linkedin':
				$link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title='
				. $title . '&summary=' . $description;
				break;

			case 'pinterest':
				$link = 'https://pinterest.com/pin/create/button/?url=' . $url . '&media='
				. $mediaUrl . '&description=' . $title . ': ' . $description;
				break;
		}

		if ($link)
		{
			$app = JFactory::getApplication();
			$app->redirect($link, '', '');
		}
	}
}

