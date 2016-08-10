<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for Shibboleth session data that needs to survive a logout during account linking 
 **/
class Migration20150331131922PlgAuthenticationShibboleth extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->db->setQuery('create table if not exists #__shibboleth_sessions(id serial not null primary key, session_key varchar(200) not null unique key, data text not null, created timestamp not null default current_timestamp)');
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->db->setQuery('drop table #__shibboleth_sessions');
		$this->db->query();
	}
}