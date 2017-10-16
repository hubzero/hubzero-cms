<?php
/**
 * EventFee
 */
class EventFee
{
	public $label;
	public $fee;
	public $earlyFee;
	public $lateFee;
	public $feeScope;

	/**
	 * Constructor
	 *
	 * @param array $params - associative array of properties/values
	 */
	public function __construct($params=array())
	{
		$this->label = (isset($params['label'])) ? $params['label'] : '';
		$this->fee = (isset($params['fee'])) ? $params['fee'] : '';
		$this->earlyFee = (isset($params['earlyFee'])) ? $params['earlyFee'] : '';
		$this->lateFee = (isset($params['lateFee'])) ? $params['lateFee'] : '';
		$this->feeScope = (isset($params['feeScope'])) ? $params['feeScope'] : '';
	}

	/**
	 * Create associate array of object from XML
	 *
	 * @static
	 * @param  SimpleXMLElement $parsedReturn - parsed XML
	 * @return
	 */
	public static function createStruct($fee)
	{
		$eventFee['label'] = (string) $fee->Label;
		$eventFee['fee'] = (string) $fee->Fee;
		$eventFee['earlyFee'] = (string) $fee->EarlyFee;
		$eventFee['lateFee'] = (string) $fee->LateFee;
		$eventFee['feeScope'] = (string) $fee->FeeScope;
		return $eventFee;
	}
}
