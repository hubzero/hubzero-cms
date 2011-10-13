<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_User
{
	private $_profile = null;
	private $_user = null;
	
	function __construct($user = null)
	{
		if (!is_null($user))
		{
			$this->_user = JUser::getInstance($user);
		}
	}
	
	function getInstance($user)
	{
		$instance = new Hubzero_User($user);

		if ($instance->_user == null)
			return null;

		return $instance;

	}
	
	private function _load_profile()
	{
		$this->_profile = Hubzero_User_Profile::getInstance($this->_user->get('username'));
	}
	
	function comparePassword($password)
	{
		if (is_null($this->_profile))
		{
			$this->_load_profile();
		}
		
		if (is_null($this->_profile))
		{
			return false;
		}
		
		$password = Hubzero_User_Helper::encrypt_password($password);
		
		if (empty($password))
		{
			return false;
		}

		if ($password === $this->_profile->get('userPassword'))
		{
			return true;
		}

		return false;
	}
	
	function getUserId()
	{
		return $this->_user->get('id');
	}
	
}
