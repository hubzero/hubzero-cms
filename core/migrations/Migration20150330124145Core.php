<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing the default database driver
 **/
class Migration20150330124145Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Update configuration, replacing mysql with pdo
		$configuration = file_get_contents(PATH_ROOT . DS . 'configuration.php');
		$configuration = preg_replace('/(var \$dbtype[\s]*=[\s]*[\'"]*)mysql([\'"]*)/', '$1pdo$2', $configuration);

		// The configuration file typically doesn't even give write permissions to the owner
		// Change that and then write it out
		$permissions = substr(decoct(fileperms(PATH_ROOT . DS . 'configuration.php')), 2);

		if (substr($permissions, 1, 1) != '6')
		{
			\JPath::setPermissions(PATH_ROOT . DS . 'configuration.php', substr_replace($permissions, '6', 1, 1));
		}

		if (!file_put_contents(PATH_ROOT . DS . 'configuration.php', $configuration))
		{
			$this->setError('Unable to write out new configuration file: permission denied', 'warning');
			return false;
		}

		// Change permissions back to what they were before
		\JPath::setPermissions(PATH_ROOT . DS . 'configuration.php', $permissions);
	}
}