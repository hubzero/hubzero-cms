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
 * Module class for com_resources data
 */
class modResources extends \Hubzero\Module\Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		$this->database = JFactory::getDBO();

		$this->database->setQuery("SELECT count(*) FROM #__resources WHERE published=5 AND standalone=1");
		$this->draftInternal = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__resources WHERE published=2 AND standalone=1");
		$this->draftUser = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__resources WHERE published=3 AND standalone=1");
		$this->pending = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__resources WHERE published=1 AND standalone=1");
		$this->published = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__resources WHERE published=0 AND standalone=1");
		$this->unpublished = $this->database->loadResult();

		$this->database->setQuery("SELECT count(*) FROM #__resources WHERE published=4 AND standalone=1");
		$this->removed = $this->database->loadResult();

		$this->css();

		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
