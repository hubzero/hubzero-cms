<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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