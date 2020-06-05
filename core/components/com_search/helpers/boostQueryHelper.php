<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/helpers/solariumBoostQueries.php";
require_once "$componentPath/models/solr/boost.php";

use Components\Search\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class BoostQueryHelper
{
	protected $boosts;

	public function __construct($args = [])
	{
		$this->boostOrm = Arr::getValue(
			$args, 'boosts', new MockProxy([
				'class' => 'Components\Search\Models\Solr\Boost'
			])
		);
		$this->queriesFactory = Arr::getValue(
			$args, 'queries', new MockProxy([
				'class' => 'Components\Search\Helpers\SolariumBoostQueries'
			])
		);
	}

	public function getAllQueries()
	{
		$boosts = $this->boostOrm->all();

		return $this->queriesFactory->one(['boosts' => $boosts]);
	}

}
