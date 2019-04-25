<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for group upload folders
 **/
class Migration20140108233319ComGroups extends Base
{
	public function up()
	{
		$old = umask(0);

		// define base path
		$base = PATH_APP . DS . 'site' . DS . 'groups';

		// make sure we have a directory
		if (!is_dir($base))
		{
			return;
		}

		// get group folders
		$groupFolders = \App::get('filesystem')->directories($base, '.', false, true);

		// make sure we have one!
		if (count($groupFolders) < 1)
		{
			return;
		}

		// loop through group folders
		foreach ($groupFolders as $groupFolder)
		{
			$groupUploadFolder = $groupFolder . DS . 'uploads';

			// make sure we havent already moved files
			if (!is_dir($groupUploadFolder))
			{
				// create uploads folder
				if (!\App::get('filesystem')->makeDirectory($groupUploadFolder))
				{
					$this->setError('Failed to create uploads folder. Try running again with elevated privileges', 'warning');
					return false;
				}
			}

			//get group files
			$groupFiles = \App::get('filesystem')->files($groupFolder);

			// move each group file
			foreach ($groupFiles as $groupFile)
			{
				$from = $groupFolder . DS . $groupFile;
				$to   = $groupUploadFolder . DS . $groupFile;
				if (!\App::get('filesystem')->move($from, $to))
				{
					$this->setError('Failed to move files to uploads folder. Try running again with elevated privileges', 'warning');
					return false;
				}
			}
		}

		umask($old);
	}
}
