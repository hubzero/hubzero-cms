<?php
class BusinessInformation extends RegistrantInformation
{
	public $fax;
	public $website;
	public $blog;
	public $company;
	public $jobTitle;
	public $department;

	/**
	 * Constructor
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
		parent::__construct();
		$this->fax = (isset($params['fax'])) ? $params['fax'] : '';
		$this->website = (isset($params['website'])) ? $params['website'] : '';
		$this->blog = (isset($params['blog'])) ? $params['blog'] : '';
		$this->company = (isset($params['company'])) ? $params['company'] : '';
		$this->jobTitle = (isset($params['jobTitle'])) ? $params['jobTitle'] : '';
		$this->department = (isset($params['department'])) ? $params['department'] : '';
	}

	/**
	 * Create an associative array of object properties from XML
	 * @static
	 * @param SimpleXMLElement $businessXml - XML of business information
	 * @return array
	 */
	public static function createStruct($businessXml)
	{
		$info = parent::createStruct($businessXml);
		$info['fax'] = (string) $businessXml->Fax;
		$info['website'] = (string) $businessXml->Website;
		$info['blog'] = (string) $businessXml->Blog;
		$info['company'] = (string) $businessXml->Company;
		$info['jobTitle'] = (string) $businessXml->JobTitle;
		$info['department'] = (string) $businessXml->Department;
		return $info;
	}
}
