<?php
/**
 * Campaign class
 */
class Campaign extends CCObject
{
	/**
	 * @var  string   $name
	 * @var  integer  $id
	 * @var  string   $link
	 * @var  unknown  $status
	 * @var  string   $campaignDate
	 * @var  string   $lastEditDate
	 * @var  string   $lastRunDate
	 * @var  string   $campaignSent
	 * @var  string   $campaignOpens
	 * @var  integer  $campaignClicks
	 * @var  integer  $campaignBounces
	 * @var  integer  $campaignForwards
	 * @var  integer  $campaignOptOuts
	 * @var  unknown  $campaignSpamReports
	 * @var  string	  $subject
	 * @var  string   $fromName
	 * @var  unknown  $campaignType
	 * @var  unknown  $vawp
	 * @var  string   $vawpLinkText
	 * @var  string   $vawpText
	 * @var  unknown  $permissionReminder
	 * @var  string   $permissionReminderText
	 * @var  unknown  $greetingSalutation
	 * @var  string	  $greetingName
	 * @var  string	  $greetingString
	 * @var  string	  $orgName
	 * @var  string   $orgAddr1
	 * @var  string   $orgAddr2
	 * @var  string   $orgAddr3
	 * @var  string   $orgCity
	 * @var  string   $orgState
	 * @var  string   $orgInternationalState
	 * @var  string   $orgCountry
	 * @var  integer  $orgPostalCode
	 * @var  string   $incForwardEmail
	 * @var  string   $forwardEmailLinkText
	 * @var  string   $incSubscribeLink
	 * @var  string   $subscribeLinkText
	 * @var  unknown  $emailContentFormat
	 * @var  string   $emailContent
	 * @var  unknown  $textVersionContent
	 * @var  unknown  $styleSheet
	 * @var  array    $lists
	 * @var  string   $fromAddress
	 * @var  string   replyAddress
	 * @var  unknown  $archiveStatus
	 * @var  unknown  $archiveUrl
	 * @var  array    $urls
	 */
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
	 *
	 * @param  array  $params  associative array of properties/values
	 */
	public function __construct($params=array())
	{
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
		$this->orgAddr2 = (isset($params['orgAddr2'])) ? $params['orgAddr2'] : '$ACCOUNT.ADDRESS_LINE_2';
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
		$this->styleSheet = (isset($params['styleSheet'])) ? $params['styleSheet'] : '';
		$this->lists = (isset($params['lists'])) ? $params['lists'] : array();
		$this->archiveStatus = (isset($params['archiveStatus'])) ? $params['archiveStatus'] : '';
		$this->archiveUrl = (isset($params['archiveUrl'])) ? $params['archiveUrl'] : '';
		$this->urls = (isset($params['urls'])) ? $params['urls'] : array();
		$this->replyAddress = (isset($params['replyAddress'])) ? $params['replyAddress'] : '';
		$this->fromAddress = (isset($params['fromAddress'])) ? $params['fromAddress'] : '';
	}

	/**
	 * Create XML Representation of the object
	 *
	 * @return  SimpleXMLElement
	 */
	public function createXml()
	{
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
		if ($campaignLists) {
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
	 *
	 * @static
	 * @param  SimpleXMLElement  $parsedReturn  parsed XML
	 * @return
	 */
	public static function createOverviewStruct($parsedReturn)
	{
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
	 *
	 * @static
	 * @param  SimpleXMLElement  $parsedReturn  parsed XML
	 * @return
	 */
	public static function createStruct($parsedReturn)
	{
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
		if ($parsedReturn->content->Campaign->ContactLists->ContactList) {
			foreach ($parsedReturn->content->Campaign->ContactLists->ContactList as $item) {
				$campaign['lists'][] = (string) $item->link->Attributes()->href;
			}
		}
		if ($parsedReturn->content->Campaign->Urls) {
			foreach ($parsedReturn->content->Campaign->Urls->Url as $link) {
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
