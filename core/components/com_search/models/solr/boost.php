<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr;

use Hubzero\Database\Relational;

/**
 * Database model for Solr Boost
 *
 * @uses  \Hubzero\Database\Relational
 */
class Boost extends Relational
{

	protected $table = '#__solr_search_boosts';

	public function getId()
	{
		return $this->get('id');
	}

	public function getType()
	{
		return $this->get('field_value');
	}

	public function getStrength()
	{
		return $this->get('strength');
	}

}
