<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Table class for tracking unity asset results
 */
class AssetUnity extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_unity', 'id', $db);

		$this->_trackAssets = false;
	}
}
