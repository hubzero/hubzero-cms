<?php
/**
 * VerifiedAddress class
 */
class VerifiedAddress extends CCObject
{
	/**
	 * @var  string   $email
	 * @var  unknown  $status
	 * @var  unknown  $verifiedTime
	 * @var  integer  $id
	 * @var  string   $link
	 * @var  unknown  $updated
	 */
	public $email;
	public $status;
	public $verifiedTime;
	public $id;
	public $link;
	public $updated;

	/**
	 * Constructor
	 *
	 * @param  array  $params  Associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->email = (isset($params['email'])) ? $params['email'] : '';
		$this->status = (isset($params['status'])) ? $params['status'] : '';
		$this->verifiedTime = (isset($params['verifiedTime'])) ? $params['verifiedTime'] : '';
		$this->id = (isset($params['id'])) ? $params['id'] : '';
		$this->link = (isset($params['link'])) ? $params['link'] : '';
		$this->updated = (isset($params['updated'])) ? $params['updated'] : '';
	}

	/**
	 * Create associate array of object from XML
	 *
	 * @static
	 * @param  SimpleXMLElement  $parsedReturn  parsed XML
	 * @return
	 */
	public static function createStruct($parsedResponse)
	{
		$address['email'] = (string) $parsedResponse->content->Email->EmailAddress;
		$address['status'] = (string) $parsedResponse->content->Email->Status;
		$address['verifiedTime'] = (string) $parsedResponse->content->Email->VerifiedTime;
		$address['id'] = (string) $parsedResponse->id;
		$address['updated'] = (string) $parsedResponse->updated;
		$address['link'] = (string) $parsedResponse->link->Attributes()->href;
		return $address;
	}
}
