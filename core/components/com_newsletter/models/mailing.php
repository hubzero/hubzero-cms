<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'mailinglist.php';
require_once __DIR__ . DS . 'newsletter.php';
require_once __DIR__ . DS . 'mailing' . DS . 'recipient.php';
require_once __DIR__ . DS . 'mailing' . DS . 'recipient' . DS . 'action.php';

/**
 * Newsletter model for a mailing
 */
class Mailing extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter';

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
		'nid' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between newsletter and mailing
	 *
	 * @return  object
	 */
	public function newsletter()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Newsletter', 'nid');
	}

	/**
	 * Defines a belongs to one relationship between mailinglist and mailing
	 *
	 * @return  object
	 */
	public function mailinglist()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Mailinglist', 'lid');
	}

	/**
	 * Get a list of recipients
	 *
	 * @return  object
	 */
	public function recipients()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Mailing\\Recipient', 'mid');
	}

	/**
	 * Get a list of actions
	 *
	 * @return  object
	 */
	public function actions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Mailing\\Recipient\\Action', 'mailingid');
	}
}
