<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for moving group blog file/image uploads to subfolder in groups uploads.
 **/
class Migration20140805223444PlgGroupsBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Import needed libraries
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$old = umask(0);

		// Define base path
		$base = JPATH_ROOT . DS . 'site' . DS . 'groups';

		// Nake sure we have a directory
		if (!is_dir($base))
		{
			return;
		}

		// Get group folders
		$groupFolders = \JFolder::folders($base, '.', false, true);

		// Make sure we have one!
		if (count($groupFolders) < 1)
		{
			return;
		}

		// Loop through group folders
		foreach ($groupFolders as $groupFolder)
		{
			$currentBlogUploadFolder = $groupFolder . DS . 'blog';
			$newBlogUploadFolder     = $groupFolder . DS . 'uploads' . DS . 'blog';

			// Skip groups without blogs folder
			if (!is_dir($currentBlogUploadFolder))
			{
				continue;
			}

			// Create new uploads folder if doesnt exist
			if (!is_dir($newBlogUploadFolder))
			{
				// create uploads folder
				if (!\JFolder::create($newBlogUploadFolder))
				{
					$this->setError('Failed to create blog uploads folder. Try running again with elevated privileges.', 'warning');
					return false;
				}
			}

			// Get group files
			$blogFiles = \JFolder::files($currentBlogUploadFolder);

			// Move each group file
			foreach ($blogFiles as $blogFile)
			{
				$from = $currentBlogUploadFolder . DS . $blogFile;
				$to   = $newBlogUploadFolder . DS . $blogFile;
				if (!\JFile::move( $from, $to ))
				{
					$this->setError('Failed to move files to blog uploads folder. Try running again with elevated privileges.', 'warning');
					return false;
				}
			}

			$res = false;
			try
			{
				$res = \JFolder::delete($currentBlogUploadFolder);
			}
			catch (Exception $e)
			{
				$this->setError('Folder deletion succeeded but failed to write to logs.', 'info');
			}

			// Delete original folder
			if (!$res)
			{
				$this->setError('Failed to delete original blog uploads folder. Try running again with elevated privileges.', 'warning');
				return false;
			}
		}

		umask($old);
	}
}