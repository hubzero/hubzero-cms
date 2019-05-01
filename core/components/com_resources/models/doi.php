<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;

/**
 * Resource DOI model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Doi extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__doi_mapping';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'doi';

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
		'rid' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'doi_shoulder'
	);

	/**
	 * Generates automatic doi_shoulder
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticDoiShoulder($data)
	{
		if (!isset($data['doi_shoulder']) || !$data['doi_shoulder'])
		{
			$data['doi_shoulder'] = \Component::params('com_tools')->get('doi_shoulder');
		}
		return $data['doi_shoulder'];
	}

	/**
	 * Get profile for author ID
	 *
	 * @return  object
	 */
	public function resource()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Entry', 'rid');
	}

	/**
	 * Get a record by its doi
	 *
	 * @param   string  $doi
	 * @return  object
	 */
	public static function oneByDoi($doi)
	{
		$row = self::all()
			->whereEquals('doi', $doi)
			->row();

		return $row;
	}

	/**
	 * Get a record by resource
	 *
	 * @param   integer  $rid
	 * @param   string   $revision
	 * @param   integer  $versionid
	 * @return  object
	 */
	public static function oneByResource($rid, $revision = null, $versionid = 0)
	{
		$model = self::all()
			->whereEquals('rid', $rid);

		if ($revision)
		{
			$model->whereEquals('local_revision', $revision);
		}

		if ($versionid)
		{
			$model->whereEquals('versionid', $versionid);
		}

		return $model->order('doi_label', 'desc')->row();
	}

	/**
	 * Register a DOI
	 *
	 * @param   array   $authors   Authors of a resource
	 * @param   object  $config    Registry
	 * @param   array   $metadata  Metadata
	 * @return  mixed   False if error, string on success
	 */
	public function register($authors, $config, $metadata = array())
	{
		if (empty($metadata))
		{
			return false;
		}

		$metadata['authors'] = $authors;

		include_once dirname(__DIR__) . '/helpers/doiService.php';

		$service = new \Components\Resources\Helpers\DoiService($metadata);

		// Register metadata
		$doi = $service->register(true, false, null, true);

		if (!$doi)
		{
			$this->addError($service->getError());
			return false;
		}
		else
		{
			$shoulder = $service->configs()->shoulder;
			if (strstr($doi, '/'))
			{
				$parts = explode('/', $doi);
				$shoulder = $parts[0];
				$doi = end($parts);
			}
			$this->set('doi', $doi);
			$this->set('doi_shoulder', $shoulder);
		}

		// Register the DOI name and URL to complete the DOI registration.
		$result = $service->register(false, true, $shoulder . '/' . $doi);

		if ($service->getError())
		{
			$this->addError($service->getError());
			return false;
		}

		return $doi;
	}
}
