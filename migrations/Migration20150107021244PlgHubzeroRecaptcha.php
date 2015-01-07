<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for clearing old recapta keys
 **/
class Migration20150107021244PlgHubzeroRecaptcha extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// delete and then add to clear old keys
		$this->deletePluginEntry('hubzero','recaptcha');
		$this->addPluginEntry('hubzero','recaptcha');
	}
}