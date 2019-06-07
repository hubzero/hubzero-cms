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
 * Migration script for moving group blog file/image uploads to subfolder in groups uploads.
 **/
class Migration20140805223444PlgGroupsBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$old = umask(0);

		// Define base path
		$base = PATH_APP . DS . 'site' . DS . 'groups';

		// Nake sure we have a directory
		if (!is_dir($base))
		{
			return;
		}

		// Get group folders
		$groupFolders = \App::get('filesystem')->directories($base, '.', false, true);

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
				if (!\App::get('filesystem')->makeDirectory($newBlogUploadFolder))
				{
					$this->setError('Failed to create blog uploads folder. Try running again with elevated privileges.', 'warning');
					return false;
				}
			}

			// Get group files
			$blogFiles = \App::get('filesystem')->files($currentBlogUploadFolder);

			// Move each group file
			foreach ($blogFiles as $blogFile)
			{
				$from = $currentBlogUploadFolder . DS . $blogFile;
				$to   = $newBlogUploadFolder . DS . $blogFile;
				if (!\App::get('filesystem')->move($from, $to))
				{
					$this->setError('Failed to move files to blog uploads folder. Try running again with elevated privileges.', 'warning');
					return false;
				}
			}

			$res = false;
			try
			{
				$res = \App::get('filesystem')->delete($currentBlogUploadFolder);
			}
			catch (\Exception $e)
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
