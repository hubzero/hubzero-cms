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
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return file type
	 *
	 * @return  string  HTML
	 */
	public function onImportAcceptedFiles()
	{
		//return '.txt <small>(' . Lang::txt('PLG_CITATION_DEFAULT_FILE') . ')</small>';
	}

	/**
	 * Import data from a file
	 *
	 * @param   array  $file
	 * @return  array
	 */
	public function onImport($file)
	{
		//array of acceptable file types
		$acceptable = array('txt');

		//get the file extension
		$extension = $file->getClientOriginalExtension();

		//only process acceptable files
		if (!in_array($extension, $acceptable))
		{
			return;
		}

		//get the file contents, uses temporary file
		$raw_contents = file_get_contents($file->getFilename());

		//check to see if this is endnote content
		if (preg_match('/%A|%0|%T/', $raw_contents))
		{
			//make new file to pass to dispatcher
			$new_file = array(
				'name'     => $file->getClientOriginalName() . '.enw',
				'type'     => $file->getType(),
				'tmp_name' => $file->getName(),
				'error'    => $file->getError(),
				'size'     => $file->getClientSize()
			);

			$return = event::trigger('citation.onImport' , array($new_file));
			return $return[0];
		}
	}
}