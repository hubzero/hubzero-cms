<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr\Filters;
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'option.php';
require_once Component::path('com_search') . '/models/solr/filters/listfilter.php';
require_once Component::path('com_search') . '/models/solr/filters/daterangefilter.php';
require_once Component::path('com_search') . '/models/solr/filters/textfieldfilter.php';

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Database model for search filters
 *
 * @uses  \Hubzero\Database\Relational
 */
class Filter extends Relational
{
	/**
	 * Table name
	 * 
	 * @var  string
	 */
	protected $table = '#__solr_search_filters';

	/**
	 * Automatic fields to populate every time a row is updated
	 *
	 * @var  array
	 */
	public $always = array(
		'params',
		'modified',
		'modified_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * children 
	 * 
	 * @return  object
	 */
	public function options()
	{
		return $this->oneToMany('\Components\Search\Models\Solr\Option', 'filter_id');
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		$params = new Registry($this->get('params'));
		return $params;
	}

	/**
	 * Make sure params are a string
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParams($data)
	{
		if (!empty($data['params']))
		{
			if (!is_string($data['params']))
			{
				if (!($data['params'] instanceof Registry))
				{
					$data['params'] = new Registry($data['params']);
				}
				$data['params'] = $data['params']->toString();
			}
			return $data['params'];
		}
	}

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		$data['modified'] = Date::of()->toSql();
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedBy($data)
	{
		$data['modified_by'] = User::getInstance()->get('id');
		return $data['modified_by'];
	}

	/**
	 * Convert facet name to solr query safe name
	 *
	 * @return  string  name of query
	 */
	public function getQueryName()
	{
		$name = str_replace(' ', '_', $this->field);
		return $name;
	}

	/**
	 * Overrides Relational method so that subclasses can be loaded depending on type
	 *
	 * @return 	object
	 */
	public function rowsFromRaw($data)
	{
		$rows = new \Hubzero\Database\Rows();
		if ($data && count($data) > 0)
		{
			foreach ($data as $row)
			{
				$filterName = ucfirst(strtolower($row->type . 'filter'));
				$className = 'Components\Search\Models\Solr\Filters\\' . $filterName;
				if (class_exists($className))
				{
					$rows->push($className::newFromResults($row));
				}
				else
				{
					$rows->push(self::newFromResults($row));
				}
			}
		}
		return $rows;
	}
}
