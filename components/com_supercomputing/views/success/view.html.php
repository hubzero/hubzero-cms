<?php

class SuperComputingViewSuccess extends SuperComputingView
{
	protected $ticket_text, $ticket_title;

	public function display()
	{

		$ticket = $this->get_partial('allocationform', 'ticket')->inherit_properties($this);
		$ticket->send();
		$this->ticket_title = $ticket->get_title();
		$this->ticket_text = $ticket->get_body(); 
		parent::display();
	}
}
