<?php
/**
 * CampaignEvent class
 */
class CampaignEvent
{
	/**
	 * @var  $id
	 * @var  $title
	 * @var  $updated
	 * @var  $contactId
	 * @var  $emailAddress
	 * @var  $campaignId
	 * @var  $campaignName
	 * @var  $campaignLink
	 * @var  $eventTime
	 */
	public $id;
	public $title;
	public $updated;
	public $contactId;
	public $emailAddress;
	public $campaignId;
	public $campaignName;
	public $campaignLink;
	public $eventTime;

	/**
	 * Constructor
	 *
	 * @param  array  $params  associative array of properties/values
	 */
	public function __construct($params = array())
	{
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

	public static function createStruct($parsedResponse, $nodeTitle)
	{
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
