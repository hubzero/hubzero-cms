<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing com_pdf2form
 **/
class Migration20150428144853ComPdf2form extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('com_pdf2form');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('com_pdf2form', 'com_pdf2form');
	}
}