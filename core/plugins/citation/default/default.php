<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @param   array    $file
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  array
	 */
	public function onImport($file, $scope = null, $scope_id = null)
	{
		// Array of acceptable file types
		$acceptable = array('txt');
		// Get the file extension
		$extension = $file->getClientOriginalExtension();

		// Only process acceptable files
		if (!in_array($extension, $acceptable))
		{
			return;
		}

		// Get the file contents, uses temporary file
		$raw_contents = file_get_contents($file->getFilename());

		// Check to see if this is endnote content
		if (preg_match('/%A|%0|%T/', $raw_contents))
		{
			// Make new file to pass to dispatcher
			$new_file = array(
				'name'     => $file->getClientOriginalName() . '.enw',
				'type'     => $file->getType(),
				'tmp_name' => $file->getName(),
				'error'    => $file->getError(),
				'size'     => $file->getClientSize()
			);

			$return = Event::trigger('citation.onImport', array($new_file), $scope, $scope_id);
			return $return[0];
		}
	}
}
