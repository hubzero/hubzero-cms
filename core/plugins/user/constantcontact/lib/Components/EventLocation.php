<?php
class EventLocation
{
	public $location;
	public $addr1;
	public $addr2;
	public $addr3;
	public $city;
	public $state;
	public $country;
	public $postalCode;

	/**
	 * Constructor
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->location = (isset($params['location'])) ? $params['location'] : '';
		$this->addr1 = (isset($params['addr1'])) ? $params['addr1'] : '';
		$this->addr2 = (isset($params['addr2'])) ? $params['addr2'] : '';
		$this->addr3 = (isset($params['addr3'])) ? $params['addr3'] : '';
		$this->city = (isset($params['city'])) ? $params['city'] : '';
		$this->state = (isset($params['state'])) ? $params['state'] : '';
		$this->country = (isset($params['country'])) ? $params['country'] : '';
		$this->postalCode = (isset($params['postalCode'])) ? $params['postalCode'] : '';
	}
}
