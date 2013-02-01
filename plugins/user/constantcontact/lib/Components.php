<?php
abstract class CCObject{
	/**
	 * Validate an object to check that all required fields have been supplied
	 * @params array $params - object property names to reference for validation before HTTP requests
	 * @return void
	 */
	protected function validate(Array $params){
		try{
			foreach($params as $field){
				if(empty($this->$field)){
					throw new CTCTException("Constant Contact ".get_class($this)." Error: '".$field."' was required but not supplied");
				}
			}
		} catch (CTCTException $e){
			$e->generateError();
		}	
	}
}
	


class ContactList extends CCObject{
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
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
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
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($parsedReturn){
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
     * @return SimpleXMLElement
     */
    public function createXml(){
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
	
	public static function createMemberStruct($parsedResponse){
		$contact['link'] = (string) $parsedResponse->link->Attributes()->href;
		$contact['id'] = (string) $parsedResponse->id;
		$contact['updated'] = (string) $parsedResponse->updated;
		$contact['emailAddress'] = (string) $parsedResponse->content->ContactListMember->EmailAddress;
		$contact['fullName'] = (string) $parsedResponse->content->ContactListMember->Name;
		return $contact;		
	}
}

class Contact extends CCObject{
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
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
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
       if(!empty($params['lists'])){
            if(is_string($params['lists'])){$this->lists[] = $params['lists'];}
            else{
                foreach($params['lists'] as $list){
                    $this->lists[] = $list;
                }
            }
        } else {$this->lists = array(); }
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($parsedReturn){
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

        if($parsedReturn->content->Contact->ContactLists){
            foreach($parsedReturn->content->Contact->ContactLists->ContactList as $list){
                $myVar = $list;
                $contact['lists'][] = (string) $list->Attributes()->id;
            }
        }
        return $contact;
    }

    /**
     * Create XML Representation of the object
     * @return SimpleXMLElement
     */
    public function createXml(){
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
        foreach($this->lists as $list){
            $listNode = $contactlists_node->addChild("ContactList");
            $listNode->addAttribute("id", $list);                  
        }

        $entry = $xml_object->asXML();
        return $entry;
    }
}

class Campaign extends CCObject{
    public $name;
    public $id;
    public $link;
    public $status;
    public $campaignDate;
    public $lastEditDate;
    public $lastRunDate;
    public $campaignSent;
    public $campaignOpens;
    public $campaignClicks;
    public $campaignBounces;
    public $campaignForwards;
    public $campaignOptOuts;
    public $campaignSpamReports;
    public $subject;
    public $fromName;
    public $campaignType;
    public $vawp;
    public $vawpLinkText;
    public $vawpText;
    public $permissionReminder;
    public $permissionReminderText;
    public $greetingSalutation;
    public $greetingName;
    public $greetingString;
    public $orgName;
    public $orgAddr1;
    public $orgAddr2;
    public $orgAddr3;
    public $orgCity;
    public $orgState;
    public $orgInternationalState;
    public $orgCountry;
    public $orgPostalCode;
    public $incForwardEmail;
    public $forwardEmailLinkText;
    public $incSubscribeLink;
    public $subscribeLinkText;
    public $emailContentFormat;
    public $emailContent;
    public $textVersionContent;
    public $styleSheet;
    public $lists = array();
    public $fromAddress;
    public $replyAddress;
    public $archiveStatus;
    public $archiveUrl;
    public $urls = array();

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params=array()){
        $this->name = (isset($params['name'])) ? $params['name'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : 'data:,';
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->status = (isset($params['status'])) ? $params['status'] : '';
        $this->campaignDate = (isset($params['campaignDate'])) ? $params['campaignDate'] : '2009-10-19T18:34:53.105Z';
        $this->lastEditDate = (isset($params['lastEditDate'])) ? $params['lastEditDate'] : '';
        $this->lastRunDate = (isset($params['lastRunDate'])) ? $params['lastRunDate'] : '';
		$this->campaignSent = (isset($params['campaignSent'])) ? $params['campaignSent'] : '';
		$this->campaignOpens = (isset($params['campaignOpens'])) ? $params['campaignOpens'] : '';
		$this->campaignClicks = (isset($params['campaignClicks'])) ? $params['campaignClicks'] : '';
		$this->campaignBounces = (isset($params['campaignBounces'])) ? $params['campaignBounces'] : '';
		$this->campaignForwards = (isset($params['campaignForwards'])) ? $params['campaignForwards'] : '';
		$this->campaignOptOuts = (isset($params['campaignOptOuts'])) ? $params['campaignOptOuts'] : '';
		$this->campaignSpamReports = (isset($params['campaignSpamReports'])) ? $params['campaignSpamReports'] : '';
		$this->subject = (isset($params['subject'])) ? $params['subject'] : '';
		$this->fromName = (isset($params['fromName'])) ? $params['fromName'] : 'Default From Name';
		$this->campaignType = (isset($params['campaignType'])) ? $params['campaignType'] : '';
		$this->vawp = (isset($params['vawp'])) ? $params['vawp'] : 'NO';
		$this->vawpLinkText = (isset($params['vawpLinkText'])) ? $params['vawpLinkText'] : '';
		$this->vawpText = (isset($params['vawpText'])) ? $params['vawpText'] : '';
		$this->permissionReminder = (isset($params['permissionReminder'])) ? $params['permissionReminder'] : 'NO';
		$this->permissionReminderText = (isset($params['permissionReminderText'])) ? $params['permissionReminderText'] : '';
		$this->greetingSalutation = (isset($params['greetingSalutation'])) ? $params['greetingSalutation'] : 'Dear';
		$this->greetingName = (isset($params['greetingName'])) ? $params['greetingName'] : 'FirstName';
		$this->greetingString = (isset($params['greetingString'])) ? $params['greetingString'] : '';
		$this->orgName = (isset($params['orgName'])) ? $params['orgName'] : '$ACCOUNT.ORGANIZATIONNAME';
		$this->orgAddr1 = (isset($params['orgAddr1'])) ? $params['orgAddr1'] : '$ACCOUNT.ADDRESS_LINE_1';
		$this->orgAddr2 = (isset($params['orgAddr2'])) ? $params['orgAddr2'] : '$ACCOUNT.ADDRESS_LINE_2';;
		$this->orgAddr3 = (isset($params['orgAddr3'])) ? $params['orgAddr3'] : '$ACCOUNT.ADDRESS_LINE_3';
		$this->orgCity = (isset($params['orgCity'])) ? $params['orgCity'] : '';
		$this->orgState = (isset($params['orgState'])) ? $params['orgState'] : '';
		$this->orgInternationalState = (isset($params['orgInternationalState'])) ? $params['orgInternationalState'] : '';
		$this->orgCountry = (isset($params['orgCountry'])) ? $params['orgCountry'] : '';
		$this->orgPostalCode = (isset($params['orgPostalCode'])) ? $params['orgPostalCode'] : '';
		$this->incForwardEmail = (isset($params['incForwardEmail'])) ? $params['incForwardEmail'] : 'NO';
		$this->forwardEmailLinkText = (isset($params['forwardEmailLinkText'])) ? $params['forwardEmailLinkText'] : '';
		$this->incSubscribeLink = (isset($params['incSubscribeLink'])) ? $params['incSubscribeLink'] : 'NO';
		$this->subscribeLinkText = (isset($params['subscribeLinkText'])) ? $params['subscribeLinkText'] : '';
		$this->emailContentFormat = (isset($params['emailContentFormat'])) ? $params['emailContentFormat'] : 'HTML';
		$this->emailContent = (isset($params['emailContent'])) ? $params['emailContent'] : '';
		$this->textVersionContent = (isset($params['textVersionContent'])) ? $params['textVersionContent'] : '';
		$this->styleSheet = (isset($params['styleSheet'])) ? $params['styleSheet'] : '';;
		$this->lists = (isset($params['lists'])) ? $params['lists'] : array();
		$this->archiveStatus = (isset($params['archiveStatus'])) ? $params['archiveStatus'] : '';
		$this->archiveUrl = (isset($params['archiveUrl'])) ? $params['archiveUrl'] : '';
        $this->urls = (isset($params['urls'])) ? $params['urls'] : array();
        $this->replyAddress = (isset($params['replyAddress'])) ? $params['replyAddress'] : '';
        $this->fromAddress = (isset($params['fromAddress'])) ? $params['fromAddress'] : '';
    }

    /**
     * Create XML Representation of the object
     * @return SimpleXMLElement
     */
    public function createXml(){
    	$this->validate(array('emailContentFormat', 'textVersionContent', 'name', 'subject', 'id'));
        $xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8'?><entry xmlns='http://www.w3.org/2005/Atom' />");
        $link = $xml->addChild("link");
        $link->addAttribute('href', '/ws/customers/customer/campaigns');
        $link->addAttribute('rel', 'edit');
        $xml->addChild("id", $this->id);
        $title = $xml->addChild("title", $this->name);
        $title->addAttribute("type", "text");
        $xml->addChild("updated", '2009-10-19T18:34:53.105Z');
        $author = $xml->addChild("author");
        $author->addChild("name", "Constant Contact");
        $content = $xml->addChild("content");
        $content->addAttribute("type", "application/vnd.ctct+xml");
        $campaign_node = $content->addChild("Campaign");
        $campaign_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
        $campaign_node->addAttribute("id", $this->id);
        $campaign_node->addChild("Name", $this->name);
        $campaign_node->addChild("Status", "draft");
        $campaign_node->addChild("Date", $this->campaignDate);
        $campaign_node->addChild("Subject", $this->subject);
        $campaign_node->addChild("FromName", $this->fromName);
        $campaign_node->addChild("ViewAsWebpage", $this->vawp);
        $campaign_node->addChild("ViewAsWebpageLinkText", $this->vawpLinkText);
        $campaign_node->addChild("ViewAsWebpageText", $this->vawpText);
        $campaign_node->addChild("PermissionReminder", $this->permissionReminder);
        $campaign_node->addChild("PermissionReminderText", $this->permissionReminderText);
        $campaign_node->addChild("GreetingSalutation", $this->greetingSalutation);
        $campaign_node->addChild("GreetingName", $this->greetingName);
        $campaign_node->addChild("GreetingString", $this->greetingString);
        $campaign_node->addChild("OrganizationName", $this->orgName);
        $campaign_node->addChild("OrganizationAddress1", $this->orgAddr1);
        $campaign_node->addChild("OrganizationAddress2", $this->orgAddr2);
        $campaign_node->addChild("OrganizationAddress3", $this->orgAddr3);
        $campaign_node->addChild("OrganizationCity", $this->orgCity);
        $campaign_node->addChild("OrganizationState", $this->orgState);
        $campaign_node->addChild("OrganizationInternationalState", $this->orgInternationalState);
        $campaign_node->addChild("OrganizationCountry", $this->orgCountry);
        $campaign_node->addChild("OrganizationPostalCode", $this->orgPostalCode);
        $campaign_node->addChild("IncludeForwardEmail", $this->incForwardEmail);
        $campaign_node->addChild("ForwardEmailLinkText", $this->forwardEmailLinkText);
        $campaign_node->addChild("IncludeSubscribeLink", $this->incSubscribeLink);
        $campaign_node->addChild("SubscribeLinkText", $this->subscribeLinkText);
        $campaign_node->addChild("EmailContentFormat", $this->emailContentFormat);
        $campaign_node->addChild("EmailContent", $this->emailContent);
        $campaign_node->addChild("EmailTextContent", $this->textVersionContent);
        $campaign_node->addChild("StyleSheet", $this->styleSheet);
        $contactLists = $campaign_node->addChild("ContactLists");
        $campaignLists = $this->lists;
        if ($campaignLists){
            foreach ($campaignLists as $list) {
                $contactList = $contactLists->addChild("ContactList");
                $contactList->addAttribute("id", $list);
                $contactLink = $contactList->addChild("link");
                $contactLink->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
                $contactLink->addAttribute("href", str_replace("http://api.constantcontact.com", "", $list));
                $contactLink->addAttribute("rel", "self");
            }
        }
        $fromEmail = $campaign_node->addChild("FromEmail");
        $email_node = $fromEmail->addChild("Email");
        $email_node->addAttribute("id", "http://api.constantcontact.com" . $this->fromAddress->link);
        $email_link = $email_node->addChild("link");
        $email_link->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
        $email_link->addAttribute("href", $this->fromAddress->link);
        $email_link->addAttribute("rel", "self");
        $fromEmail->addChild("EmailAddress", $this->fromAddress->email);
        $replyEmail = $campaign_node->addChild("ReplyToEmail");
        $replyEmailNode = $replyEmail->addChild("Email");
        $replyEmailNode->addAttribute("id", "http://api.constantcontact.com" . $this->replyAddress->link);
        $replyEmailLink = $replyEmailNode->addChild("link");
        $replyEmailLink->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
        $replyEmailLink->addAttribute("href", $this->replyAddress->link);
        $replyEmailLink->addAttribute("rel", "self");
        $replyEmail->addChild("EmailAddress", $this->replyAddress->email);
        $sourceNode = $xml->addChild("source");
        $sourceNode->addChild("id", $this->id);
        $sourceTitle = $sourceNode->addChild("title", "Campaigns for customer");
        $sourceTitle->addAttribute("type", "text");
        $sourceLink1 = $sourceNode->addChild("link");
        $sourceLink1->addAttribute("href", "campaigns");
        $sourceLink2 = $sourceNode->addChild("link");
        $sourceLink2->addAttribute("href", "campaigns");
        $sourceLink2->addAttribute("rel", "self");
        $sourceAuthor = $sourceNode->addChild("author");
        $sourceAuthor->addChild("name", 'customer');
        $sourceNode->addChild("updated", date("Y-m-d").'T'.date("H:i:s").'+01:00');
        $xml = $xml->asXML();
        return $xml;
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createOverviewStruct($parsedReturn){
        $campaign = array();
        $campaign['link'] = (string) $parsedReturn->link->Attributes()->href;
        $campaign['id'] = (string) $parsedReturn->id;
        $campaign['name'] = (string) $parsedReturn->content->Campaign->Name;
        $campaign['status'] = (string) $parsedReturn->content->Campaign->Status;
        $campaign['campaignDate'] = (string) $parsedReturn->content->Campaign->Date;
        return $campaign;
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($parsedReturn){
        $campaign = array();
        $campaign['link'] = (string) $parsedReturn->link->Attributes()->href;
        $campaign['id'] = (string) $parsedReturn->id;
        $campaign['name'] = (string) $parsedReturn->content->Campaign->Name;
        $campaign['status'] = (string) $parsedReturn->content->Campaign->Status;
        $campaign['campaignDate'] = (string) $parsedReturn->content->Campaign->Date;
        $campaign['lastEditDate'] = (string) $parsedReturn->content->Campaign->LastEditDate;
        $campaign['lastRunDate'] = (string) $parsedReturn->content->Campaign->LastRunDate;
        $campaign['campaignSent'] = (string) $parsedReturn->content->Campaign->Sent;
        $campaign['campaignOpens'] = (string) $parsedReturn->content->Campaign->Opens;
        $campaign['campaignClicks'] = (string) $parsedReturn->content->Campaign->Clicks;
        $campaign['campaignBounces'] = (string) $parsedReturn->content->Campaign->Bounces;
        $campaign['campaignForwards'] = (string) $parsedReturn->content->Campaign->Forwards;
        $campaign['campaignOptOuts'] = (string) $parsedReturn->content->Campaign->OptOuts;
        $campaign['campaignSpamReports'] = (string) $parsedReturn->content->Campaign->SpamReports;
        $campaign['subject'] = (string) $parsedReturn->content->Campaign->Subject;
        $campaign['fromName'] = (string) $parsedReturn->content->Campaign->FromName;
        $campaign['campaignType'] = (string) $parsedReturn->content->Campaign->CampaignType;
        $campaign['vawp'] = (string) $parsedReturn->content->Campaign->ViewAsWebpage;
        $campaign['vawpLinkText'] = (string) $parsedReturn->content->Campaign->ViewAsWebPageLinkText;
        $campaign['vawpText'] = (string) $parsedReturn->content->Campaign->ViewAsWebpageText;
        $campaign['permissionReminder'] = (string) $parsedReturn->content->Campaign->PermissionReminder;
        $campaign['permissionReminderTxt'] = (string) $parsedReturn->content->Campaign->PermissionReminderText;
        $campaign['greetingSalutation'] = (string) $parsedReturn->content->Campaign->GreetingSalutation;
        $campaign['greetingName'] = (string) $parsedReturn->content->Campaign->GreetingName;
        $campaign['greetingString'] = (string) $parsedReturn->content->Campaign->GreetingString;
        $campaign['orgName'] = (string) $parsedReturn->content->Campaign->OrganizationName;
        $campaign['orgAddr1'] = (string) $parsedReturn->content->Campaign->OrganizationAddress1;
        $campaign['orgAddr2'] = (string) $parsedReturn->content->Campaign->OrganizationAddress2;
        $campaign['orgAddr3'] = (string) $parsedReturn->content->Campaign->OrganizationAddress3;
        $campaign['orgCity'] =(string)  $parsedReturn->content->Campaign->OrganizationCity;
        $campaign['orgState'] = (string) $parsedReturn->content->Campaign->OrganizationState;
        $campaign['orgInternationalState'] = (string) $parsedReturn->content->Campaign->OrganizationInternationalState;
        $campaign['orgCountry'] = (string) $parsedReturn->content->Campaign->OrganizationCountry;
        $campaign['orgPostalCode'] = (string) $parsedReturn->content->Campaign->OrganizationPostalCode;
        $campaign['incForwardEmail'] = (string) $parsedReturn->content->Campaign->IncludeForwardEmail;
        $campaign['forwardEmailLinkText'] = (string) $parsedReturn->content->Campaign->ForwardEmailLinkText;
        $campaign['incSubscribeLink'] = (string) $parsedReturn->content->Campaign->IncludeSubscribeLink;
        $campaign['subscribeLinkText'] = (string) $parsedReturn->content->Campaign->SubscribeLinkText;
        $campaign['emailContentFormat'] = (string) $parsedReturn->content->Campaign->EmailContentFormat;
        $campaign['emailContent'] = (string) $parsedReturn->content->Campaign->EmailContent;
        $campaign['textVersionContent'] = (string) $parsedReturn->content->Campaign->EmailTextContent;
        $campaign['styleSheet'] = (string) $parsedReturn->content->Campaign->StyleSheet;
        $campaign['archiveStatus'] = (string) $parsedReturn->content->Campaign->ArchiveStatus;
        $campaign['archiveUrl'] = (string) $parsedReturn->content->Campaign->ArchiveURL;
        $fromAddr = new VerifiedAddress();
        $fromAddr->email = (string) $parsedReturn->content->Campaign->FromEmail->EmailAddress;
        $fromAddr->link = (string) $parsedReturn->content->Campaign->FromEmail->Email->link->Attributes()->href;
        $campaign['fromAddress'] = $fromAddr;
        $replyAddr = new VerifiedAddress();
        $replyAddr->email = (string) $parsedReturn->content->Campaign->ReplyToEmail->EmailAddress;
        $replyAddr->link = (string) $parsedReturn->content->Campaign->ReplyToEmail->Email->link->Attributes()->href;
        $campaign['replyAddress'] = $replyAddr;
        if ($parsedReturn->content->Campaign->ContactLists->ContactList){
            foreach ($parsedReturn->content->Campaign->ContactLists->ContactList as $item){
                $campaign['lists'][] = (string) $item->link->Attributes()->href;
            }
        }
        if ($parsedReturn->content->Campaign->Urls){
            foreach($parsedReturn->content->Campaign->Urls->Url as $link){
                $url['id'] = (string) $link->Attributes()->id;
                $url['value'] = (string) $link->Value;
                $url['clicks'] = (string) $link->Clicks;
                $urlArray[] = $url;
            }
            $campaign['urls'] = $urlArray;
        }
        return $campaign;
    }
}

class Folder extends CCObject{
    public $name;
    public $id;
    public $link;

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        $this->name = (isset($params['name'])) ? $params['name'] : '';
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : '';
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($parsedReturn){
        $folder['link'] = (string) $parsedReturn->link->Attributes()->href;
        $folder['id'] = (string) $parsedReturn->id;
        $folder['name'] = (string) $parsedReturn->title;
        return $folder;
    }

    /**
     * Create XML Representation of the object
     * @return SimpleXMLElement
     */
    public function createXml(){
    	$this->validate(array('name'));
        $xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><atom:entry xmlns:atom='http://www.w3.org/2005/Atom'/>");
        $content = $xml->addChild("content");
        $folder = $content->addChild("Folder", "", "");
        $folder->addChild("Name", $this->name, "");
        $entry = $xml->asXML();
        return $entry;
    }
}

class Image extends CCObject{
    public $name;
    public $id;
    public $link;
    public $updated;
    public $imageUrl;
    public $height;
    public $width;
    public $description;
    public $folder;
    public $md5hash;
    public $fileSize;
    public $fileType;

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        $this->name = (isset($params['name'])) ? $params['name'] : '';
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : '';
        $this->updated = (isset($params['updated'])) ? $params['updated'] : '';
        $this->imageUrl = (isset($params['imageUrl'])) ? $params['imageUrl'] : '';
        $this->height = (isset($params['height'])) ? $params['height'] : '';
        $this->width = (isset($params['width'])) ? $params['width'] : '';
        $this->description = (isset($params['description'])) ? $params['description'] : '';
        $this->folder = (isset($params['folder'])) ? $params['folder'] : '';
        $this->md5hash = (isset($params['md5hash'])) ? $params['md5hash'] : '';
        $this->fileSize = (isset($params['fileSize'])) ? $params['fileSize'] : '';
        $this->fileType = (isset($params['fileType'])) ? $params['fileType'] : '';
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($parsedReturn){
        $image['link'] = (string) $parsedReturn->link->Attributes()->href;
        $image['id'] = (string) $parsedReturn->id;
        $image['name'] = (string) $parsedReturn->title;
        $image['updated'] = (string) $parsedReturn->updated;
        $content = $parsedReturn->content->children();
        $image['imageUrl'] = (string) $content->Image->ImageURL;
        $image['height'] = (string) $content->Image->Height;
        $image['width'] = (string) $content->Image->Width;
        $image['description'] = (string) $content->Image->Description;
        $image['folder'] = (string) $content->Image->Folder->Name;
        $image['md5hash'] = (string) $content->Image->MD5Hash;
        $image['fileSize'] = (string) $content->Image->FileSize;
        $image['fileType'] = (string) $content->Image->FileType;
        return $image;
    }

    /**
     * Create XML Representation of the object
     * @return SimpleXMLElement
     */
    public function createXml(){
    	$this->validate(array('name', 'id'));
        $xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><atom:entry xmlns:atom='http://www.w3.org/2005/Atom'/>");
        $entry = $xml->asXML();
        return $entry;
    }
}

class VerifiedAddress extends CCObject{
    public $email;
    public $status;
    public $verifiedTime;
    public $id;
    public $link;
    public $updated;

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        $this->email = (isset($params['email'])) ? $params['email'] : '';
        $this->status = (isset($params['status'])) ? $params['status'] : '';
        $this->verifiedTime = (isset($params['verifiedTime'])) ? $params['verifiedTime'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : '';
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->updated = (isset($params['updated'])) ? $params['updated'] : '';
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($parsedResponse){
        $address['email'] = (string) $parsedResponse->content->Email->EmailAddress;
        $address['status'] = (string) $parsedResponse->content->Email->Status;
        $address['verifiedTime'] = (string) $parsedResponse->content->Email->VerifiedTime;
        $address['id'] = (string) $parsedResponse->id;
        $address['updated'] = (string) $parsedResponse->updated;
        $address['link'] = (string) $parsedResponse->link->Attributes()->href;
        return $address;
    }
}

class EventFee{
    public $label;
    public $fee;
    public $earlyFee;
    public $lateFee;
    public $feeScope;

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params=array()){
        $this->label = (isset($params['label'])) ? $params['label'] : '';
        $this->fee = (isset($params['fee'])) ? $params['fee'] : '';
        $this->earlyFee = (isset($params['earlyFee'])) ? $params['earlyFee'] : '';
        $this->lateFee = (isset($params['lateFee'])) ? $params['lateFee'] : '';
        $this->feeScope = (isset($params['feeScope'])) ? $params['feeScope'] : '';
    }

    /**
     * Create associate array of object from XML
     * @static
     * @param  SimpleXMLElement $parsedReturn - parsed XML
     * @return
     */
    public static function createStruct($fee){
        $eventFee['label'] = (string) $fee->Label;
        $eventFee['fee'] = (string) $fee->Fee;
        $eventFee['earlyFee'] = (string) $fee->EarlyFee;
        $eventFee['lateFee'] = (string) $fee->LateFee;
        $eventFee['feeScope'] = (string) $fee->FeeScope;
        return $eventFee;
    }
}

class Registrant extends CCObject{
    public $title;
    public $link;
    public $id;
    public $updated;
    public $lastName;
    public $firstName;
    public $email;
    public $personalInformation; // RegistrationInformation Type
    public $businessInformation; //RegistrationInformation Type
    public $customInformation1;
    public $customInformation2; // CustomInformation object
    public $registrationStatus;
    public $registrationDate;
    public $guestCount;
    public $paymentStatus;
    public $orderAmount;
    public $currencyType;
    public $paymentType;
    public $costs; // Array of Cost objects

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        $this->title = (isset($params['title'])) ? $params['title'] : '';
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : '';
        $this->updated = (isset($params['updated'])) ? $params['updated'] : '';
        $this->lastName = (isset($params['lastName'])) ? $params['lastName'] : '';
        $this->firstName = (isset($params['firstName'])) ? $params['firstName'] : '';
        $this->email = (isset($params['email'])) ? $params['email'] : '';
        $this->personalInformation = (isset($params['personalInformation'])) ? $params['personalInformation'] : new PersonalInformation();
        $this->businessInformation = (isset($params['businessInformation'])) ? $params['businessInformation'] : new BusinessInformation();
        $this->customInformation1 = (isset($params['customInformation1'])) ? $params['customInformation1'] : array();
        $this->customInformation2 = (isset($params['customInformation2'])) ? $params['customInformation2'] : array();
        $this->registrationStatus = (isset($params['registrationStatus'])) ? $params['registrationStatus'] : '';
        $this->registrationDate = (isset($params['registrationDate'])) ? $params['registrationDate'] : '';
        $this->guestCount = (isset($params['guestCount'])) ? $params['guestCount'] : '';
        $this->paymentStatus = (isset($params['paymentStatus'])) ? $params['paymentStatus'] : '';
		$this->ticketId = (isset($params['ticketId'])) ? $params['ticketId'] : '';
        $this->orderAmount = (isset($params['orderAmount'])) ? $params['orderAmount'] : '';
        $this->currencyType = (isset($params['currencyType'])) ? $params['currencyType'] : '';
        $this->paymentType = (isset($params['paymentType'])) ? $params['paymentType'] : '';
        $this->costs = (isset($params['costs'])) ? $params['costs'] : array();
    }

    /**
     * Create an associative array of object properties from XML
     * @static
     * @param SimpleXMLElement $parsedResponse - XML of registrant
     * @return
     */
    public static function createStruct($parsedResponse){
        $reg['title'] = (string) $parsedResponse->title;
        $reg['updated'] = (string) $parsedResponse->updated;
        $reg['link'] = (string) $parsedResponse->link->Attributes()->href;
        $reg['id'] = (string) $parsedResponse->id;
        $content = $parsedResponse->content->children();
        $reg['lastName'] = (string) $content->Registrant->LastName;
        $reg['firstName'] = (string) $content->Registrant->FirstName;
        $reg['email'] = (string) $content->Registrant->EmailAddress;
        $reg['registrationStatus'] = (string) $content->Registrant->RegistrationStatus;
        $reg['registrationDate'] = (string) $content->Registrant->RegistrationDate;
        $reg['guestCount'] = (string) $content->Registrant->GuestCount;
        $reg['paymentStatus'] = (string) $content->Registrant->PaymentStatus;
		$reg['ticketId'] = (string) $content->Registrant->TicketId;
        $reg['personalInformation'] = new PersonalInformation(PersonalInformation::createStruct($content->Registrant->PersonalInformation));
        $reg['businessInformation'] = new BusinessInformation(BusinessInformation::createStruct($content->Registrant->BusinessInformation));
        $reg['customInformation1'] = array();
        $reg['customInformation2'] = array();
        $reg['costs'] = array();
        if(isset($content->Registrant->CustomInformation1->CustomField)){
            foreach($content->Registrant->CustomInformation1->CustomField as $customInfo){
                $reg['customInformation1'][] = CustomField::createFromXml($customInfo);
            }
        }
        if(isset($content->Registrant->CustomInformation2->CustomField)){
            foreach($content->Registrant->CustomInformation2->CustomField as $customInfo){
                $reg['customInformation2'][] = CustomField::createFromXml($customInfo);
            }
        }
        if(isset($content->Registrant->Costs->Cost)){
            foreach($content->Registrant->Costs->Cost as $cost){
                $reg['costs'] = new Cost(Cost::createStruct($cost));
            }
        }
        return $reg;
    }
}

class Cost{
    public $count;
    public $feeType;
    public $rate;
    public $total;

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        $this->count = (isset($params['count'])) ? $params['count'] : '';
        $this->feeType = (isset($params['feeType'])) ? $params['feeType'] : '';
        $this->rate = (isset($params['rate'])) ? $params['rate'] : '';
        $this->total = (isset($params['total'])) ? $params['total'] : '';
    }

     /**
     * Create associative array of object properties
     * @static
     * @param array $params - Array representing an event Cost
     * @return array
     */
    public static function createStruct($params = array()){
        $cost['count'] = (string) $params->Count;
        $cost['feeType'] = (string) $params->FeeType;
        $cost['rate'] = (string) $params->Rate;
        $cost['total'] = (string) $params->Total;
        return $cost;
    }
}

class CustomField{
    public $question;
    public $answers;

    /**
     * @param string $question - registration question
     * @param array $answers -  answers for question
     */
    public function __construct($question, $answers = array()){
        $this->question = $question;
        $this->answers = $answers;
    }

    /**
     * Create CustomField object from XML
     * @static
     * @param SimpleXMLElement $parsedXml
     * @return CustomField
     */
    public static function createFromXml($parsedXml){
        $quest = (string) $parsedXml->Question;
        $answers = array();
        foreach($parsedXml->Answers->Answer as $ans){
            $answers[] = (string) $ans;
        }
        return new CustomField($quest, $answers);
    }
}

class RegistrantInformation{
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
    public function __construct($params = array()){
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
    public static function createStruct($regInfoXml){
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

class PersonalInformation extends RegistrantInformation{
    public $cellPhone;

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        parent::__construct($params);
        $this->cellPhone = (isset($params['cellPhone'])) ? $params['cellPhone'] : '';
    }

    /**
     * Create array from associative array of object properties
     * @static
     * @param SimpleXMLElement $regInfoXml - XML of registrant information
     * @return array
     */
    public static function createStruct($personalXml){
        $info = parent::createStruct($personalXml);
        $info['cellPhone'] = (string) $personalXml->CellPhone;
        return $info;
    }
}

class BusinessInformation extends RegistrantInformation{
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
    public function __construct($params = array()){
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
    public static function createStruct($businessXml){
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

class EventLocation{
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
    public function __construct($params = array()){
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

class Event extends CCObject{
    public $title;
    public $link;
    public $updated;
    public $id;
    public $name;
    public $description;
    public $registered;
    public $createdDate;
    public $status;
    public $eventType;
    public $eventLocation; // EventLocation Object
    public $registrationUrl;
    public $startDate;
    public $endDate;
    public $publishDate;
    public $attendedCount;
    public $cancelledCount;
    public $eventFeeRequired;
    public $currencyType;
    public $paymentOptions; // PaymentOptions Object
    public $registrationTypes; // array of RegistrationType objects

    /**
     * Constructor
     * @param array $params - associative array of properties/values
     */
    public function __construct($params = array()){
        $this->title = (isset($params['title'])) ? $params['title'] : '';
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->name = (isset($params['name'])) ? $params['name'] : '';
        $this->updated = (isset($params['updated'])) ? $params['updated'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : '';
        $this->description = (isset($params['description'])) ? $params['description'] : '';
        $this->registered = (isset($params['registered'])) ? $params['registered'] : '';
        $this->createdDate = (isset($params['createdDate'])) ? $params['createdDate'] : '';
        $this->status = (isset($params['status'])) ? $params['status'] : '';
        $this->eventType = (isset($params['eventType'])) ? $params['eventType'] : '';
        $this->eventLocation = (isset($params['eventLocation'])) ? $params['eventLocation'] : new EventLocation();
        $this->registrationUrl = (isset($params['registrationUrl'])) ? $params['registrationUrl'] : '';
        $this->startDate = (isset($params['startDate'])) ? $params['startDate'] : '';
        $this->endDate = (isset($params['endDate'])) ? $params['endDate'] : '';
        $this->publishDate = (isset($params['publishDate'])) ? $params['publishDate'] : '';
        $this->attendedCount = (isset($params['attendedCount'])) ? $params['attendedCount'] : '';
        $this->cancelledCount = (isset($params['cancelledCount'])) ? $params['cancelledCount'] : '';
        $this->eventFeeRequired = (isset($params['eventFeeRequired'])) ? $params['eventFeeRequired'] : '';
        $this->currencyType = (isset($params['currencyType'])) ? $params['currencyType'] : '';
        $this->paymentOptions = (isset($params['paymentOptions'])) ? $params['paymentOptions'] : array();
        $this->registrationTypes = (isset($params['registrationTypes'])) ? $params['registrationTypes'] : array(new RegistrationType());
    }

    /**
     * Create an associative array of object properties from XML
     * @static
     * @param SimpleXMLElement $parsedResponse - XML of an event
     * @return array
     */
    public static function createStruct($parsedResponse){
        $event['link'] = (string) $parsedResponse->link->Attributes()->href;
        $event['updated'] = (string) $parsedResponse->updated;
        $event['id'] = (string) $parsedResponse->id;
        $eventNode = $parsedResponse->content->children();
        $event['name'] = (string) $eventNode->Event->Name;
        $event['description'] = (string) $eventNode->Event->Description;
        $event['title'] = (string) $eventNode->Event->Title;
        $event['registered'] = (string) $eventNode->Event->Registered;
        $event['createdDate'] = (string) $eventNode->Event->CreatedDate;
        $event['status'] = (string) $eventNode->Event->Status;
        $event['eventType'] = (string) $eventNode->Event->EventType;
        $location['location'] =  (string) $eventNode->Event->EventLocation->Location;
        $location['addr1'] =  (string) $eventNode->Event->EventLocation->Address1;
        $location['addr2'] = (string) $eventNode->Event->EventLocation->Address2;
        $location['addr3'] = (string) $eventNode->Event->EventLocation->Address3;
        $location['city'] = (string) $eventNode->Event->EventLocation->City;
        $location['state'] = (string) $eventNode->Event->EventLocation->State;
        $location['country'] = (string) $eventNode->Event->EventLocation->Country;
        $location['postalCode'] = (string) $eventNode->Event->EventLocation->PostalCode;
        $event['eventLocation'] = new EventLocation($location);
        $event['registrationUrl'] = (string) $eventNode->Event->RegistrationURL;
        $event['startDate'] = (string) $eventNode->Event->StartDate;
        $event['endDate'] = (string) $eventNode->Event->EndDate;
        $event['publishDate'] = (string) $eventNode->Event->PublishDate;
        $event['attendedCount'] = (string) $eventNode->Event->AttendedCount;
        $event['cancelledCount'] = (string) $eventNode->Event->CancelledCount;
        $event['eventFeeRequired'] = (string) $eventNode->Event->EventFeeRequired;
        $event['currencyType'] = (string) $eventNode->Event->CurrencyType;
        $event['paymentOptions'] = array();
        if(isset($eventNode->Event->PaymentOptions->PaymentOption)){
            foreach($eventNode->Event->PaymentOptions->PaymentOption as $options){
                $payOption = '';
                $type = (string) $options->Type;
                try{
                    if($type == "PAYPAL"){
                        $paypalAddr = (string) $options->PayPalAccountEmail;
                        $payOption = new PayPalPayment($paypalAddr);
                    } elseif ($type == "CHECK"){
                        $payOption = new CheckPayment(CheckPayment::createStruct($options));
                    } elseif ($type == "DOOR"){
                        $payOption = new DoorPayment();
                    }
                    if (empty($payOption)){throw new Exception('Payment Type '.$type.' is not a valid option');}
                } catch (Exception $e) {
                    die($e->getMessage());
                }
                    $event['paymentOptions'][] = $payOption;
            }
            foreach($eventNode->Event->RegistrationTypes->RegistrationType as $regType){
                $registration = new RegistrationType(RegistrationType::createStruct($regType));
            }
            $event['registrationTypes'][] = $registration;
        }
        return $event;
    }
}

class PaymentOption{}

class PayPalPayment extends PaymentOption{
    public $type;
    public $payPalEmail;

    /**
     * Constructor
     * @param string $paypalAddress - Paypal email address
     */
    public function __construct($paypalAddress){
        $this->type = "PAYPAL";
        $this->payPalEmail = $paypalAddress;
    }
}

class DoorPayment extends PaymentOption{
    public $type;

    /**
     * Constructor
     */
    public function __construct(){
        $this->type = "DOOR";
    }
}

class CheckPayment extends PaymentOption{
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
    public function __construct($params=array()){
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
    public static function createStruct($parsedReturn){
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

class RegistrationType{
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
    public function __construct($params=array()){
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
    public static function createStruct($parsedResponse){
        $registrationType['name'] = (string) $parsedResponse->Name;
        $registrationType['registrationLimit'] = (string) $parsedResponse->RegistrationLimitCount;
        $registrationType['registrationClosedManually'] = (string) $parsedResponse->RegistrationClosedManually;
        $registrationType['guestLimit'] = (string) $parsedResponse->GuestLimit;
        $registrationType['ticketing'] = (string) $parsedResponse->Ticketing;
        $registrationType['eventFees'] = array();
        foreach($parsedResponse->EventFees->EventFee as $fee){
            $eventFee = new EventFee(EventFee::createStruct($fee));
            $registrationType['eventFees'][] = $eventFee;
        }
        return $registrationType;
    }
}

class CampaignEvent{
    public $id;
    public $title;
    public $updated;
    public $contactId;
    public $emailAddress;
    public $campaignId;
    public $campaignName;
    public $campaignLink;
    public $eventTime;

    public function __construct($params = array()){
        $this->id = (isset($params['id'])) ? $params['id'] : '';
        $this->title = (isset($params['title'])) ? $params['title'] : '';
        $this->updated = (isset($params['updated'])) ? $params['updated'] : '';
        $this->contactId = (isset($params['contactId'])) ? $params['contactId'] : '';
        $this->emailAddress = (isset($params['emailAddress'])) ? $params['emailAddress'] : '';
        $this->campaignId = (isset($params['campaignId'])) ? $params['campaignId'] : '';
        $this->campaignName = (isset($params['campaignName'])) ? $params['campaignName'] : '';
        $this->campaignLink = (isset($params['campaignLink'])) ? $params['campaignLink'] : '';
        $this->eventTime = (isset($params['eventTime'])) ? $params['eventTime'] : '';
    }

    public static function createStruct($parsedResponse, $nodeTitle){
        $event['id'] = (string) $parsedResponse->id;
        $event['title'] = (string) $parsedResponse->title;
        $event['updated'] = (string) $parsedResponse->updated;
        $event['contactId'] = (string) $parsedResponse->content->$nodeTitle->Contact->Attributes()->id;
        $event['emailAddress'] = (string) $parsedResponse->content->$nodeTitle->Contact->EmailAddress;
        $event['campaignId'] = (string) $parsedResponse->content->$nodeTitle->Campaign->Attributes()->id;
        $event['campaignLink'] = (string)$parsedResponse->content->$nodeTitle->Campaign->link->Attributes()->href;
        $event['campaignName'] = (string) $parsedResponse->content->$nodeTitle->Campaign->Name;
        $event['eventTime'] = (string) $parsedResponse->content->$nodeTitle->EventTime;
        return $event;
    }
}

class Schedule extends CCObject{
    public $link;
    public $id;
    public $updated;
    public $time;
    public $campaign;

    public function __construct($params = array()){
        $this->link = (isset($params['link'])) ? $params['link'] : '';
        $this->id = (isset($params['id'])) ? $params['id'] : '';
        $this->updated = (isset($params['updated'])) ? $params['updated'] : '';
        $this->time = (isset($params['time'])) ? $params['time'] : '';
        $this->campaign = (isset($params['campaign'])) ? $params['campaign'] : '';
    }
    
    public function createXml(){
    	$this->validate(array('time'));
        $xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><entry xmlns='http://www.w3.org/2005/Atom'/>");
        $linkNode = $xml->addChild("link");
        $linkNode->addAttribute('href', $this->campaign->link.'/schedules/1');
        $linkNode->addAttribute('rel', 'edit');
        $xml->addChild("id", $this->campaign->id);
        $xml->addChild("title", $this->time);
        $xml->addChild("updated", $this->time);
        $author = $xml->addChild("author");
        $author->addChild("name", "ConstantContact");
        $content = $xml->addChild("content");
        $content->addAttribute("type", "application/vnd.ctct+xml");
        $schedule = $content->addChild("Schedule", "", 'http://ws.constantcontact.com/ns/1.0/');
        $schedule->addAttribute("id", $this->campaign->link.'/schedules/1');
        $schedule->addChild("ScheduledTime", $this->time);
        return $xml->asXML();
    }

    public static function createStruct($parsedResponse){
        $schedule['link'] = (string) $parsedResponse->link->Attributes()->href;
        $schedule['id'] = (string) $parsedResponse->id;
        $schedule['updated'] = (string) $parsedResponse->updated;
        $schedule['time'] = (string) $parsedResponse->content->Schedule->ScheduledTime;
        return $schedule;
    }
}

class Utility{
    /**
     * Find the URL of the provided object
     * @static
     * @param mixed $item
     * @return string
     */
    public static function findUrl($item){
        $return = null;
         try{
            if (is_string($item)){$return = $item;}
            elseif (is_object($item)){$return = 'https://api.constantcontact.com'.$item->link;}
            if($return == null){ throw new CTCTException('Constant Contact Error: Unable to determine which url to access');}
         } catch (CTCTException $e){
            $e->generateError();
         }
          return $return;
    }

    /**
     * Find the next link from collection XML
     * @static
     * @param SimpleXMLElement $item
     * @return string - valid nextlink to be used, else false if none could be found
     */
    public static function findNextLink($item){
        $nextLink = $item->xpath("//*[@rel='next']");
        return ($nextLink) ? (string) $nextLink[0]->Attributes()->href : false;
    }
}

class CTCTException extends Exception{
    public function __construct($message, $code = 0, Exception $previous = null){
        parent::__construct($message, $code);
    }

    public function generateError($msgPrefix=null){
        $this->logError($this->message);
        echo $msgPrefix.' '.$this->getMessage().'<br />';
    }

    private function logError($errorText, $file="error.log"){
        date_default_timezone_set('America/New_York');
        $message = "Constant Contact Exception -- ".date("F j, Y, g:i:sa")."\n".$errorText."\n";
        $message .= "Stack Trace: ".$this->getTraceAsString()."\n";
        error_log($message."\n", 3, $file);
    }
}
