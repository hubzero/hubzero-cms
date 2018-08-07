<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing tables for DrWho component
 **/
class Migration20180807000000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		print("Hello, World!\n");
		$path = PATH_APP . DS . "site" . DS . "publications";
		print("Exploring " . $path . ":\n");
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		foreach ($objects as $name => $object)
		{
			if (!$object->isDir())
			{
				$bits = explode(DS, $name);
				array_pop($bits);
				if (end($bits) === "links")
				{
					print($name . "\n");
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		echo "Goodbye, World!";
	}
}
