<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130715111246ModIncrementalRegistration extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$queries = array(
			'alter table #__profile_completion_awards add column mailpreferenceoption int not null default 0',
			'insert into #__incremental_registration_labels(field, label) values (\'mailPreferenceOption\', \'E-Mail Updates\')'
		);

		foreach ($queries as $query)
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$queries = array(
			'alter table #__profile_completion_awards drop column mailpreferenceoption',
			'delete from #__incremental_registration_labels where field = \'mailPreferenceOption\''
		);

		foreach ($queries as $query)
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}
