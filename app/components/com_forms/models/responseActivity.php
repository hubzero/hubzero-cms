<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Models;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/responseFeedItem.php";

use Components\Forms\Models\ResponseFeedItem;

class ResponseActivity extends ResponseFeedItem
{

	/**
	 * Validation rules
	 *
	 * @var   array
	 */
	protected $rules = [
		'action' => 'notempty',
		'response_id' => 'notempty'
	];

	/**
	 * Constructs ResponseEvent instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$state = [];

		$state['created'] = Date::toSql();
		$state['created_by'] = User::get('id');
		$state['scope'] = self::$ACTIVITY_SCOPE;

		$this->set($state);

		parent::__construct();
	}

}
