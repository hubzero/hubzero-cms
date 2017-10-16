<?php
class CheckPayment extends PaymentOption
{
	public $type;
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
	public function __construct($params=array())
	{
		$this->type = "CHECK";
		$this->addr1 = ($params['addr1']) ? $params['addr1'] : '';
		$this->addr2 = ($params['addr2']) ? $params['addr2'] : '';
		$this->addr3 = ($params['addr3']) ? $params['addr3'] : '';
		$this->city = ($params['city']) ? $params['city'] : '';
		$this->state = ($params['state']) ? $params['state'] : '';
		$this->country = ($params['country']) ? $params['country'] : '';
		$this->postalCode = ($params['postalCode']) ? $params['postalCode'] : '';
	}

	/**
	 * Create an associative array of object properties from XML
	 * @static
	 * @param SimpleXMLElement $parsedReturn - XML of a check payment
	 * @return array
	 */
	public static function createStruct($parsedReturn)
	{
		$paymentAddr['addr1'] = (string) $parsedReturn->PaymentAddress->Address1;
		$paymentAddr['addr2'] = (string) $parsedReturn->PaymentAddress->Address2;
		$paymentAddr['addr3'] = (string) $parsedReturn->PaymentAddress->Address3;
		$paymentAddr['city'] = (string) $parsedReturn->PaymentAddress->City;
		$paymentAddr['state'] = (string) $parsedReturn->PaymentAddress->State;
		$paymentAddr['country'] = (string) $parsedReturn->PaymentAddress->Country;
		$paymentAddr['postalCode'] = (string) $parsedReturn->PaymentAddress->PostalCode;
		return $paymentAddr;

	}
}
