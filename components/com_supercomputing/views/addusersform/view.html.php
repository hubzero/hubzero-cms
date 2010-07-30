<?php

class SuperComputingViewAddUsersForm extends SuperComputingView
{
	public function display()
	{
		$this->request_type = 'add_users';
		$this->get_partial('peopleform')->inherit_properties($this)->display();
		$this->get_partial('formtail')->inherit_properties($this)->display();
	}
}
