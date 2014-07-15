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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class GroupsHelperDocumentRendererScript extends GroupsHelperDocumentRenderer
{
	/**
	 * Render content to group template
	 *
	 * @param    string
	 */
	public function render()
	{
		// get stylehseet base & source
		$base   = (isset($this->params->base)) ? strtolower($this->params->base) : 'uploads';
		$source = (isset($this->params->source)) ? trim($this->params->source) : null;

		// if we want base to be template,
		// shortcut to template/assets/css folder
		if ($base == 'template')
		{
			$base = 'template' . DS . 'assets' . DS . 'js';
		}

		// we must have a source
		if ($source === null)
		{
			return;
		}

		// get download path for source (serve up file)
		if ($path = $this->group->downloadLinkForPath($base, $source))
		{
			// add stylsheet to document
			$document = JFactory::getDocument();
			$document->addScript($path);
		}
	}
}