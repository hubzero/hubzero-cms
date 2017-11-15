<?php
class PersonalInformation extends RegistrantInformation
{
	public $cellPhone;

	/**
	 * Constructor
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
		parent::__construct($params);
		$this->cellPhone = (isset($params['cellPhone'])) ? $params['cellPhone'] : '';
	}

	/**
	 * Create array from associative array of object properties
	 * @static
	 * @param SimpleXMLElement $regInfoXml - XML of registrant information
	 * @return array
	 */
	public static function createStruct($personalXml)
	{
		$info = parent::createStruct($personalXml);
		$info['cellPhone'] = (string) $personalXml->CellPhone;
		return $info;
	}
}
