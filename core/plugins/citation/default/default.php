<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
	public function onImport($file, $scope = NULL, $scope_id = NULL)
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

			$return = event::trigger('citation.onImport' , array($new_file), $scope, $scope_id);
			return $return[0];
		}
	}
}
