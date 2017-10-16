<?php
/**
 * Cost class
 */
class Cost
{
	/**
	 * @var  integer  $count
	 * @var  unknown  $feeType
	 * @var  unknown  $rate
	 * @var  integer  $total
	 */
	public $count;
	public $feeType;
	public $rate;
	public $total;

	/**
	 * Constructor
	 *
	 * @param  array  $params  Associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->count = (isset($params['count'])) ? $params['count'] : '';
		$this->feeType = (isset($params['feeType'])) ? $params['feeType'] : '';
		$this->rate = (isset($params['rate'])) ? $params['rate'] : '';
		$this->total = (isset($params['total'])) ? $params['total'] : '';
	}

	 /**
	 * Create associative array of object properties
	 *
	 * @static
	 * @param  array  $params  Array representing an event Cost
	 * @return array
	 */
	public static function createStruct($params = array())
	{
		$cost['count'] = (string) $params->Count;
		$cost['feeType'] = (string) $params->FeeType;
		$cost['rate'] = (string) $params->Rate;
		$cost['total'] = (string) $params->Total;
		return $cost;
	}
}

/**
 * CustomField class
 */
class CustomField
{
	/**
	 * @var  string   $question
	 * @var  unknown  $answers
	 */
	public $question;
	public $answers;

	/**
	 * @param  string  $question  Registration question
	 * @param  array   $answers   Answers for question
	 */
	public function __construct($question, $answers = array())
	{
		$this->question = $question;
		$this->answers = $answers;
	}

	/**
	 * Create CustomField object from XML
	 *
	 * @static
	 * @param   SimpleXMLElement  $parsedXml
	 * @return  CustomField
	 */
	public static function createFromXml($parsedXml)
	{
		$quest = (string) $parsedXml->Question;
		$answers = array();
		foreach ($parsedXml->Answers->Answer as $ans) {
			$answers[] = (string) $ans;
		}
		return new CustomField($quest, $answers);
	}
}

class RegistrantInformation
{
	/**
	 * @var  $label
	 * @var  $addr1
	 * @var  $addr2
	 * @var  $addr3
	 * @var  $city
	 * @var  $state
	 * @var  $postalCode
	 * @var  $province
	 * @var  $country
	 * @var  $phone
	 */
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
	 *
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
	 *
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

/**
 * PersonalInformation class
 */
class PersonalInformation extends RegistrantInformation
{
	public $cellPhone;

	/**
	 * Constructor
	 *
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
		parent::__construct($params);
		$this->cellPhone = (isset($params['cellPhone'])) ? $params['cellPhone'] : '';
	}

	/**
	 * Create array from associative array of object properties
	 *
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

/**
 * BusinessInformation class
 */
class BusinessInformation extends RegistrantInformation
{
	/**
	 * @var  $fax
	 * @var  $website
	 * @var  $blog
	 * @var  $company
	 * @var  $jobTitle
	 * @var  $department
	 * @var  
	 */
	public $fax;
	public $website;
	public $blog;
	public $company;
	public $jobTitle;
	public $department;

	/**
	 * Constructor
	 *
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
	 *
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

/**
 * EventLocation class
 */
class EventLocation
{
	/**
	 * @var  $location
	 * @var  $addr1
	 * @var  $addr2
	 * @var  $addr3
	 * @var  $city
	 * @var  $state
	 * @var  $country
	 * @var  $postalCode
	 */
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
	 *
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
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

/**
 * CCEvent class
 */
class CCEvent extends CCObject
{
	/**
	 * @var  $title
	 * @var  $link
	 * @var  $updated
	 * @var  $id
	 * @var  $name
	 * @var  $description
	 * @var  $registered
	 * @var  $createdDate
	 * @var  $status
	 * @var  $eventType
	 * @var  $eventLocation
	 * @var  $registrationUrl
	 * @var  $startDate
	 * @var  $endDate
	 * @var  $publishDate
	 * @var  $attendedCount
	 * @var  $cancelledCount
	 * @var  $eventFeeRequired
	 * @var  $currencyType
	 * @var  $paymentOptions
	 * @var   $registrationTypes
		 */
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
	 *
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params = array())
	{
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
	 *
	 * @static
	 * @param SimpleXMLElement $parsedResponse - XML of an event
	 * @return array
	 */
	public static function createStruct($parsedResponse)
	{
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
		if (isset($eventNode->Event->PaymentOptions->PaymentOption)) {
			foreach ($eventNode->Event->PaymentOptions->PaymentOption as $options) {
				$payOption = '';
				$type = (string) $options->Type;
				try {
					if ($type == "PAYPAL") {
						$paypalAddr = (string) $options->PayPalAccountEmail;
						$payOption = new PayPalPayment($paypalAddr);
					} elseif ($type == "CHECK") {
						$payOption = new CheckPayment(CheckPayment::createStruct($options));
					} elseif ($type == "DOOR") {
						$payOption = new DoorPayment();
					}
					if (empty($payOption)) {throw new Exception('Payment Type '.$type.' is not a valid option');}
				} catch (Exception $e) {
					die($e->getMessage());
				}
					$event['paymentOptions'][] = $payOption;
			}
			foreach ($eventNode->Event->RegistrationTypes->RegistrationType as $regType) {
				$registration = new RegistrationType(RegistrationType::createStruct($regType));
			}
			$event['registrationTypes'][] = $registration;
		}
		return $event;
	}
}

/**
 * PyamentOption class
 */
class PaymentOption
{
}

class PayPalPayment extends PaymentOption
{
	/**
	 * @var   $type
	 * @var  $payPalEmail
	 */
	public $type;
	public $payPalEmail;

	/**
	 * Constructor
	 *
	 * @param  string  $paypalAddress  Paypal email address
	 */
	public function __construct($paypalAddress)
	{
		$this->type = "PAYPAL";
		$this->payPalEmail = $paypalAddress;
	}
}

/**
 * DoorPayment class
 */
class DoorPayment extends PaymentOption
{
	/**
	 * @var   $type
	 */
	public $type;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = "DOOR";
	}
}

class CheckPayment extends PaymentOption
{
	/**
	 * @var  $type
	 * @var  $addr1
	 * @var  $addr2
	 * @var  $addr3
	 * @var  $city
	 * @var  $state
	 * @var  $country
	 * @var  $postalCode
	 */
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
	 *
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
	 *
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

/**
 * RegistrationType
 */
class RegistrationType
{
	/**
	 * @var  $name
	 * @var  $registrationLimit
	 * @var  $registrationClosedManually
	 * @var  $guestLimit
	 * @var  $ticketing
	 * @var  $eventFees
	 */
	public $name;
	public $registrationLimit;
	public $registrationClosedManually;
	public $guestLimit;
	public $ticketing;
	public $eventFees;

	/**
	 * Constructor
	 *
	 * @param  array  $params  associative array of properties/values
	 */
	public function __construct($params=array())
	{
		$this->name = (isset($params['name'])) ? $params['name'] : '';
		$this->registrationLimit = (isset($params['registrationLimit'])) ? $params['registrationLimit'] : '';
		$this->registrationClosedManually = (isset($params['registrationClosedManually'])) ? $params['registrationClosedManually'] : '';
		$this->guestLimit = (isset($params['guestLimit'])) ? $params['guestLimit'] : '';
		$this->ticketing = (isset($params['ticketing'])) ? $params['ticketing'] : '';
		$this->eventFees = (isset($params['eventFees'])) ? $params['eventFees'] : array();
	}

	/**
	 * Create an associative array of object properties from XML
	 *
	 * @static
	 * @param SimpleXMLElement $parsedResponse - XML of a registration type
	 * @return array
	 */
	public static function createStruct($parsedResponse)
	{
		$registrationType['name'] = (string) $parsedResponse->Name;
		$registrationType['registrationLimit'] = (string) $parsedResponse->RegistrationLimitCount;
		$registrationType['registrationClosedManually'] = (string) $parsedResponse->RegistrationClosedManually;
		$registrationType['guestLimit'] = (string) $parsedResponse->GuestLimit;
		$registrationType['ticketing'] = (string) $parsedResponse->Ticketing;
		$registrationType['eventFees'] = array();
		foreach ($parsedResponse->EventFees->EventFee as $fee)
		{
			$eventFee = new EventFee(EventFee::createStruct($fee));
			$registrationType['eventFees'][] = $eventFee;
		}
		return $registrationType;
	}
}
