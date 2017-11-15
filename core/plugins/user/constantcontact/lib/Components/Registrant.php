<?php
/**
 * Registrant class
 */
class Registrant extends CCObject
{
	/**
	 * @var  string   $title
	 * @var  string   $link
	 * @var  integer  $id
	 * @var  unknown  $updated
	 * @var  string   $lastName
	 * @var  string   $firstName
	 * @var  string   $email
	 * @var  unknown  $personalInformation
	 * @var  unknown  $businessInformation
	 * @var  $customInformation1
	 * @var  $customInformation2
	 * @var  $registrationStatus
	 * @var  $registrationDate
	 * @var  integer  $guestCount
	 * @var  $paymentStatus
	 * @var  integer  $orderAmount
	 * @var  $currencyType
	 * @var  $paymentType
	 * @var  $costs
	 */
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
	 *
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
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
	 *
	 * @static
	 * @param SimpleXMLElement $parsedResponse - XML of registrant
	 * @return
	 */
	public static function createStruct($parsedResponse)
	{
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
		if (isset($content->Registrant->CustomInformation1->CustomField)) {
			foreach ($content->Registrant->CustomInformation1->CustomField as $customInfo) {
				$reg['customInformation1'][] = CustomField::createFromXml($customInfo);
			}
		}
		if (isset($content->Registrant->CustomInformation2->CustomField)) {
			foreach ($content->Registrant->CustomInformation2->CustomField as $customInfo) {
				$reg['customInformation2'][] = CustomField::createFromXml($customInfo);
			}
		}
		if (isset($content->Registrant->Costs->Cost)) {
			foreach ($content->Registrant->Costs->Cost as $cost) {
				$reg['costs'] = new Cost(Cost::createStruct($cost));
			}
		}
		return $reg;
	}
}
