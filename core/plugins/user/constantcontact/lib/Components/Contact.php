<?php
/**
 * Contact class
 */
class Contact extends CCObject
{
	/**
	 * @var  string   $link
	 * @var  integer  $id
	 * @var  unknown  $updated  
	 * @var  $status
	 * @var  string   $emailAddress
	 * @var  unknown  $emailType
	 * @var  string   $firstName
	 * @var  string   $middleName
	 * @var  string   $lastName
	 * @var  string   $fullName
	 * @var  string   $jobTitle
	 * @var  string   $companyName
	 * @var  $homePhone
	 * @var  $workPhone
	 * @var  string   $addr1
	 * @var  string   $addr2
	 * @var  string   $addr3
	 * @var  string   $city
	 * @var  integer  $stateCode
	 * @var  string   $stateName
	 * @var  integer  $countryCode
	 * @var  string   $countryName
	 * @var  integer  $postalCode
	 * @var  integer  $subPostalCode
	 * @var  string   $notes
	 * @var  $customField1
	 * @var  $customField2
	 * @var  $customField3
	 * @var  $customField4
	 * @var  $customField5
	 * @var  $customField6
	 * @var  $customField7
	 * @var  $customField8
	 * @var  $customField9
	 * @var  $customField10
	 * @var  $customField11
	 * @var  $customField12
	 * @var  $customField13
	 * @var  $customField14
	 * @var  $customField15
	 * @var  $contactLists
	 * @var  $confirmed
	 * @var  $optInSource
	 */
	public $link;
	public $id;
	public $updated;
	public $status;
	public $emailAddress;
	public $emailType;
	public $firstName;
	public $middleName;
	public $lastName;
	public $fullName;
	public $jobTitle;
	public $companyName;
	public $homePhone;
	public $workPhone;
	public $addr1;
	public $addr2;
	public $addr3;
	public $city;
	public $stateCode;
	public $stateName;
	public $countryCode;
	public $countryName;
	public $postalCode;
	public $subPostalCode;
	public $notes;
	public $customField1;
	public $customField2;
	public $customField3;
	public $customField4;
	public $customField5;
	public $customField6;
	public $customField7;
	public $customField8;
	public $customField9;
	public $customField10;
	public $customField11;
	public $customField12;
	public $customField13;
	public $customField14;
	public $customField15;
	public $contactLists;
	public $confirmed;
	public $optInSource;
	public $lists = array();

	/**
	 * Constructor
	 *
	 * @param  array  $params  associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->link = (isset($params['link'])) ? $params['link'] : '';
		$this->id = (isset($params['id'])) ? $params['id'] : 'data:,';
		$this->updated = (isset($params['updated'])) ? $params['updated'] : '';
		$this->status = (isset($params['status'])) ? $params['status'] : '';
		$this->emailAddress = (isset($params['emailAddress'])) ? $params['emailAddress'] : '';
		$this->emailType = (isset($params['emailType'])) ? $params['emailType'] : '';
		$this->firstName = (isset($params['firstName'])) ? $params['firstName'] : '';
		$this->middleName = (isset($params['middleName'])) ? $params['middleName'] : '';
		$this->lastName = (isset($params['lastName'])) ? $params['lastName'] : '';
		$this->fullName = (isset($params['fullName'])) ? $params['fullName']: '';
		$this->jobTitle = (isset($params['jobTitle'])) ? $params['jobTitle']: '';
		$this->companyName = (isset($params['companyName'])) ? $params['companyName']: '';
		$this->homePhone = (isset($params['homePhone'])) ? $params['homePhone']: '';
		$this->workPhone = (isset($params['workPhone'])) ? $params['workPhone']: '';
		$this->addr1 = (isset($params['addr1'])) ? $params['addr1']: '';
		$this->addr2 = (isset($params['addr2'])) ? $params['addr2']: '';
		$this->addr3 = (isset($params['addr3'])) ? $params['addr3']: '';
		$this->city = (isset($params['city'])) ? $params['city']: '';
		$this->stateCode = (isset($params['stateCode'])) ? $params['stateCode']: '';
		$this->stateName = (isset($params['stateName'])) ? $params['stateName']: '';
		$this->countryCode = (isset($params['countryCode'])) ? $params['countryCode']: '';
		$this->countryName = (isset($params['countryName'])) ? $params['countryName']: '';
		$this->postalCode = (isset($params['postalCode'])) ? $params['postalCode']: '';
		$this->subPostalCode = (isset($params['subPostalCode'])) ? $params['subPostalCode']: '';
		$this->notes = (isset($params['notes'])) ? $params['notes']: '';
		$this->customField1 = (isset($params['customField1'])) ? $params['customField1']: '';
		$this->customField2 = (isset($params['customField2'])) ? $params['customField2']: '';
		$this->customField3 = (isset($params['customField3'])) ? $params['customField3']: '';
		$this->customField4 = (isset($params['customField4'])) ? $params['customField4']: '';
		$this->customField5 = (isset($params['customField5'])) ? $params['customField5']: '';
		$this->customField6 = (isset($params['customField6'])) ? $params['customField6']: '';
		$this->customField7 = (isset($params['customField7'])) ? $params['customField7']: '';
		$this->customField8 = (isset($params['customField8'])) ? $params['customField8']: '';
		$this->customField9 = (isset($params['customField9'])) ? $params['customField9']: '';
		$this->customField10 = (isset($params['customField10'])) ? $params['customField10']: '';
		$this->customField11 = (isset($params['customField11'])) ? $params['customField11']: '';
		$this->customField12 = (isset($params['customField12'])) ? $params['customField12']: '';
		$this->customField13 = (isset($params['customField13'])) ? $params['customField13']: '';
		$this->customField14 = (isset($params['customField14'])) ? $params['customField14']: '';
		$this->customField15 = (isset($params['customField15'])) ? $params['customField15']: '';
		$this->contactLists = (isset($params['contactLists'])) ? $params['contactLists']: '';
		$this->confirmed = (isset($params['confirmed'])) ? $params['confirmed']: '';
		$this->optInSource = (isset($params['optInSource'])) ? $params['optInSource']: 'ACTION_BY_CUSTOMER';
		if (!empty($params['lists']))
		{
			if (is_string($params['lists']))
			{
				$this->lists[] = $params['lists'];
			}
			else
			{
				foreach ($params['lists'] as $list)
				{
					$this->lists[] = $list;
				}
			}
		}
		else
		{
			$this->lists = array();
		}
	}

	/**
	 * Create associate array of object from XML
	 *
	 * @static
	 * @param   SimpleXMLElement  $parsedReturn  parsed XML
	 * @return  array
	 */
	public static function createStruct($parsedReturn)
	{
		$contact['link'] = (string) $parsedReturn->link->Attributes()->href;
		$contact['id'] = (string) $parsedReturn->id;
		$contact['updated'] = (string) $parsedReturn->updated;
		$contact['emailAddress'] = (string) $parsedReturn->content->Contact->EmailAddress;
		$contact['firstName'] = (string) $parsedReturn->content->Contact->FirstName;
		$contact['lastName'] = (string) $parsedReturn->content->Contact->LastName;
		$contact['middleName'] = (string) $parsedReturn->content->Contact->MiddleName;
		$contact['companyName'] = (string) $parsedReturn->content->Contact->CompanyName;
		$contact['optInSource'] = (string) $parsedReturn->content->Contact->OptInSource;
		$contact['jobTitle'] = (string) $parsedReturn->content->Contact->JobTitle;
		$contact['homePhone'] = (string) $parsedReturn->content->Contact->HomePhone;
		$contact['workPhone'] = (string) $parsedReturn->content->Contact->WorkPhone;
		$contact['addr1'] = (string) $parsedReturn->content->Contact->Addr1;
		$contact['addr2'] = (string) $parsedReturn->content->Contact->Addr2;
		$contact['addr3'] = (string) $parsedReturn->content->Contact->Addr3;
		$contact['city'] = (string) $parsedReturn->content->Contact->City;
		$contact['stateCode'] = (string) $parsedReturn->content->Contact->StateCode;
		$contact['stateName'] = (string) $parsedReturn->content->Contact->StateName;
		$contact['countryCode'] = (string) $parsedReturn->content->Contact->CountryCode;
		$contact['postalCode'] = (string) $parsedReturn->content->Contact->PostalCode;
		$contact['subPostalCode'] = (string) $parsedReturn->content->Contact->SubPostalCode;
		$contact['customField1'] = (string) $parsedReturn->content->Contact->CustomField1;
		$contact['customField2'] = (string) $parsedReturn->content->Contact->CustomField2;
		$contact['customField3'] = (string) $parsedReturn->content->Contact->CustomField3;
		$contact['customField4'] = (string) $parsedReturn->content->Contact->CustomField4;
		$contact['customField5'] = (string) $parsedReturn->content->Contact->CustomField5;
		$contact['customField6'] = (string) $parsedReturn->content->Contact->CustomField6;
		$contact['customField7'] = (string) $parsedReturn->content->Contact->CustomField7;
		$contact['customField8'] = (string) $parsedReturn->content->Contact->CustomField8;
		$contact['customField9'] = (string) $parsedReturn->content->Contact->CustomField9;
		$contact['customField10'] = (string) $parsedReturn->content->Contact->CustomField10;
		$contact['customField11'] = (string) $parsedReturn->content->Contact->CustomField11;
		$contact['customField12'] = (string) $parsedReturn->content->Contact->CustomField12;
		$contact['customField13'] = (string) $parsedReturn->content->Contact->CustomField13;
		$contact['customField14'] = (string) $parsedReturn->content->Contact->CustomField14;
		$contact['customField15'] = (string) $parsedReturn->content->Contact->CustomField15;
		$contact['notes'] = (string) $parsedReturn->content->Contact->Note;
		$contact['emailType'] = (string) $parsedReturn->content->Contact->EmailType;
		$contact['status'] = (string) $parsedReturn->content->Contact->Status;

		if ($parsedReturn->content->Contact->ContactLists)
		{
			foreach ($parsedReturn->content->Contact->ContactLists->ContactList as $list)
			{
				$myVar = $list;
				$contact['lists'][] = (string) $list->Attributes()->id;
			}
		}
		return $contact;
	}

	/**
	 * Create XML Representation of the object
	 *
	 * @return  SimpleXMLElement
	 */
	public function createXml()
	{
		$this->validate(array('emailAddress', 'lists'));
		$update_date = '2008-07-23T14:21:06.407Z';
		$xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><entry xmlns='http://www.w3.org/2005/Atom'></entry>";
		$xml_object = simplexml_load_string($xml_string);
		$xml_object->addChild("title");
		$xml_object->addChild("updated",$update_date);
		$author_node = $xml_object->addChild("author");
		$author_node->addChild("name", ("CTCT Samples"));
		$xml_object->addChild("id", $this->id);
		$summary_node = $xml_object->addChild("summary");
		$summary_node->addAttribute("type", "text");
		$content_node = $xml_object->addChild("content");
		$content_node->addAttribute("type", "application/vnd.ctct+xml");
		$contact_node = $content_node->addChild("Contact");
		$contact_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
		$contact_node->addChild("EmailAddress", $this->emailAddress);
		$contact_node->addChild("FirstName", $this->firstName);
		$contact_node->addChild("LastName", $this->lastName);
		$contact_node->addChild("MiddleName", $this->middleName);
		$contact_node->addChild("CompanyName", $this->companyName);
		$contact_node->addChild("JobTitle", $this->jobTitle);
		$contact_node->addChild("OptInSource", $this->optInSource);
		$contact_node->addChild("HomePhone",$this->homePhone);
		$contact_node->addChild("WorkPhone", $this->workPhone);
		$contact_node->addChild("Addr1", $this->addr1);
		$contact_node->addChild("Addr2", $this->addr2);
		$contact_node->addChild("Addr3", $this->addr3);
		$contact_node->addChild("City", $this->city);
		$contact_node->addChild("StateCode", $this->stateCode);
		$contact_node->addChild("StateName", $this->stateName);
		$contact_node->addChild("CountryCode", $this->countryCode);
		$contact_node->addChild("PostalCode", $this->postalCode);
		$contact_node->addChild("SubPostalCode", $this->subPostalCode);
		$contact_node->addChild("Note", $this->notes);
		$contact_node->addChild("EmailType", $this->emailType);
		$contact_node->addChild("CustomField1", $this->customField1);
		$contact_node->addChild("CustomField2", $this->customField2);
		$contact_node->addChild("CustomField3", $this->customField3);
		$contact_node->addChild("CustomField4", $this->customField4);
		$contact_node->addChild("CustomField5", $this->customField5);
		$contact_node->addChild("CustomField6", $this->customField6);
		$contact_node->addChild("CustomField7", $this->customField7);
		$contact_node->addChild("CustomField8", $this->customField8);
		$contact_node->addChild("CustomField9", $this->customField9);
		$contact_node->addChild("CustomField10", $this->customField10);
		$contact_node->addChild("CustomField11", $this->customField11);
		$contact_node->addChild("CustomField12", $this->customField12);
		$contact_node->addChild("CustomField13", $this->customField13);
		$contact_node->addChild("CustomField14", $this->customField14);
		$contact_node->addChild("CustomField15", $this->customField15);
		$contactlists_node = $contact_node->addChild("ContactLists");
		foreach ($this->lists as $list) {
			$listNode = $contactlists_node->addChild("ContactList");
			$listNode->addAttribute("id", $list);
		}

		$entry = $xml_object->asXML();
		return $entry;
	}
}
