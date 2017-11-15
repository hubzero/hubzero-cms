<?php
/**
 * RegistrationType class
 */
class RegistrationType
{
	public $name;
	public $registrationLimit;
	public $registrationClosedManually;
	public $guestLimit;
	public $ticketing;
	public $eventFees;

	/**
	 * Constructor
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params=array())
	{
		$this->name = (isset($params['name'])) ? $params['name'] : '';
		$this->registrationLimit = (isset($params['registrationLimit'])) ? $params['registrationLimit'] : '';
		$this->registrationClosedManually = (isset($params['registrationClosedManually'])) ? $params['registrationClosedManually'] : '';
		$this->guestLimit = (isset($params['guestLimit'])) ? $params['guestLimit'] : '';
		$this->ticketing = (isset($params['ticketing'])) ? $params['ticketing'] : '';
		$this->eventFees = (isset($params['eventFees'])) ? $params['eventFees'] : array();
	}

	/**
	 * Create an associative array of object properties from XML
	 * @static
	 * @param SimpleXMLElement $parsedResponse - XML of a registration type
	 * @return array
	 */
	public static function createStruct($parsedResponse)
	{
		$registrationType['name'] = (string) $parsedResponse->Name;
		$registrationType['registrationLimit'] = (string) $parsedResponse->RegistrationLimitCount;
		$registrationType['registrationClosedManually'] = (string) $parsedResponse->RegistrationClosedManually;
		$registrationType['guestLimit'] = (string) $parsedResponse->GuestLimit;
		$registrationType['ticketing'] = (string) $parsedResponse->Ticketing;
		$registrationType['eventFees'] = array();
		foreach ($parsedResponse->EventFees->EventFee as $fee)
		{
			$eventFee = new EventFee(EventFee::createStruct($fee));
			$registrationType['eventFees'][] = $eventFee;
		}
		return $registrationType;
	}
}
