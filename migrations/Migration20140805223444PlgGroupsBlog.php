<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140805223444PlgGroupsBlog extends Base
{
	/**
	 * Up
	 **/
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
			$currentBlogUploadFolder = $groupFolder . DS . 'blog';
			$newBlogUploadFolder     = $groupFolder . DS . 'uploads' . DS . 'blog';

			// skip groups without blogs folder
			if (!is_dir($currentBlogUploadFolder))
			{
				continue;
			}

			// create new uploads folder if doesnt exist
			if (!is_dir( $newBlogUploadFolder ))
			{
				// create uploads folder
				if (!\JFolder::create( $newBlogUploadFolder ))
				{
					$return = new \stdClass();
					$return->error = new \stdClass();
					$return->error->type = 'warning';
					$return->error->message = 'Failed to create blog uploads folder. Try running again with elevated privileges';
					return $return;
				}
			}

			//get group files
			$blogFiles = \JFolder::files( $currentBlogUploadFolder );

			// move each group file
			foreach ($blogFiles as $blogFile)
			{
				$from = $currentBlogUploadFolder . DS . $blogFile;
				$to   = $newBlogUploadFolder . DS . $blogFile;
				if (!\JFile::move( $from, $to ))
				{
					$return = new \stdClass();
					$return->error = new \stdClass();
					$return->error->type = 'warning';
					$return->error->message = 'Failed to move files to blog uploads folder. Try running again with elevated privileges';
					return $return;
				}
			}

			// delete original folder
			if (!\JFolder::delete($currentBlogUploadFolder))
			{
				$return = new \stdClass();
				$return->error = new \stdClass();
				$return->error->type = 'warning';
				$return->error->message = 'Failed to delete original blog uploads folder. Try running again with elevated privileges';
				return $return;
			}
		}

		umask($old);
	}

	/**
	 * Down
	 **/
	public function down()
	{

	}
}