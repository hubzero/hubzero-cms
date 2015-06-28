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
		else if (!is_dir($old))
		{
			$this->setError('A site directory in the old location does not appear to exist.', 'warning');
			return false;
		}
		else if (is_dir($new))
		{
			$this->setError('A site directory already exists in app.', 'warning');
			return false;
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
		else if (!is_dir($old))
		{
			$this->setError('A site directory in the app location does not appear to exist.', 'warning');
			return false;
		}
		else if (is_dir($new))
		{
			$this->setError('A site directory already exists in root.', 'warning');
			return false;
		}
	}
}