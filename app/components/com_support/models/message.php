<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use Config;

/**
 * Support ticket message model
 */
class Message extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'   => 'notempty',
		'message' => 'notempty'
	);

	/**
	 * Get message
	 *
	 * @param   integer  $ticket_id
	 * @return  string
	 */
	public function transformMessage($ticket_id = 0)
	{
		$content = $this->get('message');

		$content = str_replace('"', '&quot;', stripslashes($content));
		$content = str_replace('&quote;', '&quot;', $content);
		$content = str_replace('#XXX', '#' . $ticket_id, $content);
		$content = str_replace('{ticket#}', $ticket_id, $content);
		$content = str_replace('{sitename}', Config::get('sitename'), $content);
		$content = str_replace('{siteemail}', Config::get('mailfrom'), $content);

		return $content;
	}
}
