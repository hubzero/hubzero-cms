<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\MyMessages;

use Hubzero\Module\Module;
use Hubzero\Message\Recipient;
use JFactory;

/**
 * Module class for displaying the latest messages
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$this->juser = JFactory::getUser();
		$database = JFactory::getDBO();

		$this->moduleclass = $this->params->get('moduleclass');
		$this->limit = intval($this->params->get('limit', 10));

		// Find the user's most recent support tickets
		$recipient = new Recipient($database);
		$this->rows  = $recipient->getUnreadMessages($this->juser->get('id'), $this->limit);
		$this->total = count($recipient->getUnreadMessages($this->juser->get('id')));

		if ($recipient->getError())
		{
			$this->setError($recipient->getError());
		}

		// Push the module CSS to the template
		$this->css();

		require $this->getLayoutPath();
	}
}

