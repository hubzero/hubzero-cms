<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/csvFile.php";

use Components\Forms\Helpers\CsvFile;

class CsvFileFactory
{

	/**
	 * Instantiates CsvFile object
	 *
	 * @param    string   $path   File path
	 * @return   object
	 */
	public function create($path)
	{
		return new CsvFile(['path' => $path]);
	}

}
