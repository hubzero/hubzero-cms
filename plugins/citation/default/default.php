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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

/**
 * Citations plugin class for bibtex
 */
class plgCitationDefault extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return file type
	 *
	 * @return     string HTML
	 */
	public function onImportAcceptedFiles()
	{
		//return '.txt <small>(' . JText::_('PLG_CITATION_DEFAULT_FILE') . ')</small>';
	}

	/**
	 * Short description for 'onImport'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $file Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function onImport($file)
	{
		//array of acceptable file types
		$acceptable = array('txt');

		//get the file extension
		$file_info = pathinfo($file['name']);

		//only process acceptable files
		if (!in_array($file_info['extension'], $acceptable))
		{
			return;
		}

		//get the file contents
		$raw_contents = file_get_contents($file['tmp_name']);

		//check to see if this is endnote content
		if (preg_match('/%A|%0|%T/', $raw_contents))
		{
			//load citation import plugins
			JPluginHelper::importPlugin('citation');
	        $dispatcher = JDispatcher::getInstance();

			//make new file to pass to dispatcher
			$new_file = array(
				'name'     => $file_info['filename'] . '.enw',
				'type'     => $file['type'],
				'tmp_name' => $file['tmp_name'],
				'error'    => $file['error'],
				'size'     => $file['size']
			);

			$return = $dispatcher->trigger('onImport' , array($new_file));
			return $return[0];
		}
	}
}