<?php
class RegistrantInformation
{
	public $label;
	public $addr1;
	public $addr2;
	public $addr3;
	public $city;
	public $state;
	public $postalCode;
	public $province;
	public $country;
	public $phone;

	/**
	 * Constructor
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->label = (isset($params['label'])) ? $params['label'] : '';
		$this->addr1 = (isset($params['addr1'])) ? $params['addr1'] : '';
		$this->addr2 = (isset($params['addr2'])) ? $params['addr2'] : '';
		$this->addr3 = (isset($params['addr3'])) ? $params['addr3'] : '';
		$this->city = (isset($params['city'])) ? $params['city'] : '';
		$this->state = (isset($params['state'])) ? $params['state'] : '';
		$this->postalCode = (isset($params['postalCode'])) ? $params['postalCode'] : '';
		$this->province = (isset($params['province'])) ? $params['province'] : '';
		$this->country = (isset($params['country'])) ? $params['country'] : '';
		$this->phone = (isset($params['phone'])) ? $params['phone'] : '';
	}

	/**
	 * Create associative array of object properties from XML
	 * @static
	 * @param SimpleXMLElement $regInfoXml - XML of registrant information
	 * @return array
	 */
	public static function createStruct($regInfoXml)
	{
		$info['label'] = (string) $regInfoXml->Label;
		$info['addr1'] = (string) $regInfoXml->Address1;
		$info['addr2'] = (string) $regInfoXml->Address2;
		$info['addr3'] = (string) $regInfoXml->Address3;
		$info['city'] = (string) $regInfoXml->City;
		$info['state'] = (string) $regInfoXml->State;
		$info['postalCode'] = (string) $regInfoXml->Zip;
		$info['province'] = (string) $regInfoXml->Province;
		$info['country'] = (string) $regInfoXml->Country;
		$info['phone'] = (string) $regInfoXml->Phone;
		return $info;
	}
}
