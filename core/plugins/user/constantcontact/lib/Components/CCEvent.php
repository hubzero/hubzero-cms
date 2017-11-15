<?php
class CCEvent extends CCObject
{
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
