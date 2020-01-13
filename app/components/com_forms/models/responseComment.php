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

class ResponseComment extends ResponseFeedItem
{

	/**
	 * Constructs ResponseComment instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$state = [];

		$state['created'] = Date::toSql();
		$state['created_by'] = User::get('id');
		$state['description'] = $args['content'];
		$state['action'] = 'comment';
		$state['scope'] = self::$ACTIVITY_SCOPE;
		$state['scope_id'] = $args['response_id'];

		$this->set($state);

		parent::__construct();
	}

}
