<?php

class SuperComputingViewFormTail extends SuperComputingView 
{
	protected function show_captcha() { return JFactory::getUser()->guest; }
	protected function get_submit_label()
	{
		switch ($this->request_type)
		{
			case 'new':       return NEW_SUBMIT_LABEL;
			case 'renew':     return RENEW_SUBMIT_LABEL;
			case 'add_users': return ADD_USERS_SUBMIT_LABEL;
			default:          return NULL;
		}
	}
}
