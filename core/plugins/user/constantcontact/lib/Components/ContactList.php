<?php
/**
 * ContactList class
 */
class ContactList extends CCObject
{
	/**
	 * @var integer  $contactCount
	 * @var unknown  $displayOnSignup
	 * @var integer  $id
	 * @var string   $link
	 * @var string   $name
	 * @var unknown  $optInDefault
	 * @var unknown  $sortOrder
	 * @var boolean  $updated  Maybe string?
	 */
	public $contactCount;
	public $displayOnSignup;
	public $id;
	public $link;
	public $name;
	public $optInDefault;
	public $sortOrder;
	public $updated;

	/**
	 * Constructor
	 *
	 * @param  array  $params  Associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->contactCount = (isset($params['contactCount'])) ? $params['contactCount'] : '';
		$this->displayOnSignup = (isset($params['displayOnSignup'])) ? $params['displayOnSignup'] : '';
		$this->id = (isset($params['id'])) ? $params['id'] : 'data:,';
		$this->link = (isset($params['link'])) ? $params['link'] : '';
		$this->name = (isset($params['name'])) ? $params['name'] : '';
		$this->optInDefault = (isset($params['optInDefault'])) ? $params['optInDefault'] : '';
		$this->sortOrder = (isset($params['sortOrder'])) ? $params['sortOrder'] : '99';
		$this->updated = (isset($params['updated'])) ? $params['updated'] : '';
	}

	/**
	 * Create associate array of object from XML
	 *
	 * @static
	 * @param  SimpleXMLElement  $parsedReturn  parsed XML
	 * @return array
	 */
	public static function createStruct($parsedReturn)
	{
		$list['link'] = (string) $parsedReturn->link->Attributes()->href;
		$list['id'] = (string) $parsedReturn->id;
		$list['optInDefault'] = (string) $parsedReturn->content->ContactList->OptInDefault;
		$list['name'] = (string) $parsedReturn->content->ContactList->Name;
		$list['displayOnSignup'] = (string) $parsedReturn->content->ContactList->DisplayOnSignup;
		$list['sortOrder'] = (string) $parsedReturn->content->ContactList->SortOrder;
		$list['contactCount'] = (string) $parsedReturn->content->ContactList->ContactCount;
		return $list;
	}

	/**
	 * Create XML Representation of the object
	 *
	 * @return SimpleXMLElement
	 */
	public function createXml()
	{
		$this->validate(array('name', 'id'));
		$xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><entry xmlns='http://www.w3.org/2005/Atom'></entry>";
		$xml = simplexml_load_string($xml_string);
		$xml->addChild("id", $this->id);
		$xml->addChild("title");
		$xml->addChild("author");
		$xml->addChild("updated", "2008-04-16");
		$contentNode = $xml->addChild("content");
		$contentNode->addAttribute("type", "application/vnd.ctct+xml");
		$listNode = $contentNode->addChild("ContactList");
		$listNode->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
		$listNode->addChild("OptInDefault", $this->optInDefault);
		$listNode->addChild("Name", $this->name);
		$listNode->addChild("SortOrder", $this->sortOrder);
		return $xml->asXML();
	}

	/**
	 * Create associative array of object from XML?
	 *
	 * @param   object  $parsedResponse
	 * @return  array
	 */
	public static function createMemberStruct($parsedResponse)
	{
		$contact['link'] = (string) $parsedResponse->link->Attributes()->href;
		$contact['id'] = (string) $parsedResponse->id;
		$contact['updated'] = (string) $parsedResponse->updated;
		$contact['emailAddress'] = (string) $parsedResponse->content->ContactListMember->EmailAddress;
		$contact['fullName'] = (string) $parsedResponse->content->ContactListMember->Name;
		return $contact;
	}
}
