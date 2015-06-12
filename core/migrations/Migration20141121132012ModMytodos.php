<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding a "My To-Do Items" module contributed by Shaun Einolf <einolfs@mail.nih.gov>
 **/
class Migration20141121132012ModMytodos extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mytodos', 1, '');
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mytodos');
	}
}