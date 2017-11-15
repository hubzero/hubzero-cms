<?php
class Cost
{
	public $count;
	public $feeType;
	public $rate;
	public $total;

	/**
	 * Constructor
	 * @param array $params - associative array of properties/values
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
	 * @static
	 * @param array $params - Array representing an event Cost
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
