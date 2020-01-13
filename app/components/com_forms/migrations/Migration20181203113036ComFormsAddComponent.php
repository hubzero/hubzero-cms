<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20181203113036ComFormsAddComponent extends Base
{

	public function up()
	{
		$this->addComponentEntry('forms');
	}

	public function down()
	{
		$this->deleteComponentEntry('forms');
	}

}
