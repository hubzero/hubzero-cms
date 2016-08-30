<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling digest plugins
 * Specifically the event, feedaggregator, and resource plugins
 **/
class Migration201603091905401ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$elements = array('event','feedaggregator','resource');
		foreach ($elements as $element)
		{
			$this->addPluginEntry('newsletter', $element);
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$elements = array('event','feedaggregator','resource');
		foreach ($elements as $element)
		{
			$this->deletePluginEntry('newsletter', $element);
		}
	}
}
