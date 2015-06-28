<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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