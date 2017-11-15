<?php
class DoorPayment extends PaymentOption
{
	public $type;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = "DOOR";
	}
}
