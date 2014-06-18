<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JLoader::import('Hubzero.Controller');

class OAuthControllerAuthorize extends \Hubzero\Component\SiteController
{
	public function authorizeTask()
	{
		$oauth_token = JRequest::getVar('oauth_token');

		if (empty($oauth_token))
		{
			JError::raiseError(403, 'Forbidden');
		}

		$db = JFactory::getDBO();

		$db->setQuery("SELECT * FROM #__oauthp_tokens WHERE token="	.
			$db->Quote($oauth_token) .
			" AND user_id=0 LIMIT 1;");

		$result = $db->loadObject();

		if ($result === false)
		{
			JError:raiseError(500, 'Internal Server Error');
		}

		if (empty($result))
		{
			JError::raiseError(403, 'Forbidden');
		}

		if (JRequest::getMethod() == 'GET')
		{
			$this->view->oauth_token = $oauth_token;
			$this->view->display();
			return;
		}

		if (JRequest::getMethod() == 'POST')
		{
			$token = JRequest::get('token',''.'post');

			if ($token != sha1($this->verifier))
			{
				JError::raiseError(403, 'Forbidden');
			}

			echo "posted";

			return;
		}

		JError::raiseError(405, 'Method Not Allowed');
	}
}