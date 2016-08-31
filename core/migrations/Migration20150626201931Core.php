<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving site directory
 **/
class Migration20150626201931Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$old = PATH_ROOT . DS . 'site';
		$new = PATH_ROOT . DS . 'app' . DS . 'site';

		// Make sure the old dir exists and the new one doesn't
		if (is_dir($old) && !is_dir($new))
		{
			// Now, check and make sure we're on the same filesystem
			$oldStat = stat($old);
			$newStat = stat(PATH_ROOT . DS . 'app');

			if ($oldStat && $newStat && $oldStat['dev'] == $newStat['dev'])
			{
				if (!rename($old, $new))
				{
					$this->setError('Failed to move site to the app directory.', 'fatal');
					return false;
				}
			}
			else
			{
				$this->setError('The site directory cannot be moved (perhaps because it is on a different file system). Please move manually and mark this migration as having been run.', 'fatal');
				return false;
			}
		}
		else if (!is_dir($old) && !is_dir($new))
		{
			if (!mkdir($new, 0775))
			{
				$this->setError('Unable to create new site directory (there was no old site directory).', 'warning');
				return false;
			}
		}
		else if (is_dir($old) && is_dir($new))
		{
			$this->setError('A site directory already exists in app and an old site directory also exists. Please manually move your site data and mark this migration as having been run.', 'fatal');
			return false;
		}
		else if (!is_dir($old) && is_dir($new))
		{
			/* Migration has already been handled by some other means */
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$old = PATH_ROOT . DS . 'app' . DS . 'site';
		$new = PATH_ROOT . DS . 'site';

		// Make sure the old dir exists and the new one doesn't
		if (is_dir($old) && !is_dir($new))
		{
			// Now, check and make sure we're on the same filesystem
			$oldStat = stat(PATH_ROOT . DS . 'app');
			$newStat = stat($new);

			if ($oldStat && $newStat && $oldStat['dev'] == $newStat['dev'])
			{
				if (!rename($old, $new))
				{
					$this->setError('Failed to move site to the root directory.', 'fatal');
					return false;
				}
			}
			else
			{
				$this->setError('The site directory cannot be moved (perhaps because it is on a different file system). Please move manually and mark this migration as having been run.', 'fatal');
				return false;
			}
		}
		else if (!is_dir($old) && !is_dir($new))
		{
			if (!mkdir($new,0775))
			{
				$this->setError('Unable to create new site directory (there was no old site directory).', 'warning');
				return false;
			}
		}
		else if (is_dir($old) && is_dir($new))
		{
			$this->setError('A site directory already exists in root and in app. Please manually move your site data and mark this migration as having been run.', 'fatal');
			return false;
		}
		else if (!is_dir($old) && is_dir($new))
		{
			/* Migration has already been handled by some other means */
		}
	}
}
