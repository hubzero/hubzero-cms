<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for group upload folders
 **/
class Migration20140108233319ComGroups extends Base
{
	public function up()
	{
		// import needed libraries
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$old = umask(0);

		// define base path
		$base = JPATH_ROOT . DS . 'site' . DS . 'groups';

		// make sure we have a directory
		if (!is_dir($base))
		{
			return;
		}

		// get group folders
		$groupFolders = \JFolder::folders( $base, '.', false, true );

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
			if (!is_dir( $groupUploadFolder ))
			{
				// create uploads folder
				if (!\JFolder::create( $groupUploadFolder ))
				{
					$this->setError('Failed to create uploads folder. Try running again with elevated privileges', 'warning');
					return false;
				}
			}

			//get group files
			$groupFiles = \JFolder::files( $groupFolder );

			// move each group file
			foreach ($groupFiles as $groupFile)
			{
				$from = $groupFolder . DS . $groupFile;
				$to   = $groupUploadFolder . DS . $groupFile;
				if (!\JFile::move( $from, $to ))
				{
					$this->setError('Failed to move files to uploads folder. Try running again with elevated privileges', 'warning');
					return false;
				}
			}
		}

		umask($old);
	}
}
