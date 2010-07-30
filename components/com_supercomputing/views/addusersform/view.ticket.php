<?php

class SuperComputingViewAddUsersForm extends SuperComputingTicketView
{
	public function get_title()
	{
		return 'Supercomputing allocation user addition request for '.$this->fields['pi']['last-name'].', '.$this->fields['pi']['first-name'].' at '.$this->fields['pi']['organization']; 
	}

	public function display()
	{
		$this->user =& JFactory::getUser();
		parent::display('ticket');
	}
}
