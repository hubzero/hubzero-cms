<?php

class SuperComputingViewPeopleForm extends SuperComputingView 
{
	protected function get_page_heading()
	{
		switch ($this->request_type)
		{
			case 'new':       return NEW_REQUEST_TEXT;
			case 'renew':     return RENEW_REQUEST_TEXT;
			case 'add_users': return ADD_USERS_REQUEST_TEXT;
			default:          return NULL;
		}
	}
}
