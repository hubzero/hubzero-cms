<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Antispam Content Plugin
 */
class plgContentAntispam extends JPlugin
{
	/**
	 * Finder before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @param	bool		If the content is just about to be created
	 * @since   2.5
	 */
	public function onContentBeforeSave($context, $content, $isNew)
	{
		if (is_object($content))
		{
			return;
		}

		if (!($s = $this->params->get('service')))
		{
			return;
		}

		$service = new \Hubzero\Antispam\Service($s);
		$service->set('apiPublicKey', $this->params->get('apiPublicKey'));
		$service->set('apiPrivateKey', $this->params->get('apiPrivateKey'));
		if ($properties = $this->params->get('options'))
		{
			$options = array();

			$lines = explode("\n", $properties);
			foreach ($lines as $line)
			{
				$bits = explode('=', $line);
				if (count($bits) <= 1) 
				{
					continue;
				}
				$options[trim($bits[0])] = trim($bits[1]);
			}
			$service->setProperties($options);
		}

		/*
		$service->set('user_email', $item->creator('email'));
		$service->set('user_id', $item->creator('id'));
		$service->set('user_name', $item->creator('name'));
		*/

		if ($service->isSpam($content))
		{
			return false;
		}
	}
}
