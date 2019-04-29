<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for forks
 */
class plgPublicationsForks extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();

		if ($publication->_category->_params->get('plg_forks'))
		{
			$areas['forks'] = Lang::txt('PLG_PUBLICATIONS_FORKS');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'     => '',
			'metadata' => '',
			'name'     => $this->_name
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onPublicationAreas($publication))
			 && !array_intersect($areas, array_keys($this->onPublicationAreas($publication))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$publication->_category->_params->get('plg_forks'))
		{
			return $arr;
		}

		if ($rtrn != 'metadata')
		{
			$action = strtolower(Request::getWord('action', ''));

			switch ($action)
			{
				case 'fork':
					$arr['html'] = $this->_fork($publication);
					break;

				default:
					$arr['html'] = $this->_forks($publication);
					break;
			}

			if (Request::getInt('no_html', 0))
			{
				ob_clean();
				echo $arr['html'];
				exit();
			}
		}

		return $arr;
	}

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   integer  $miniview     View style
	 * @return  array
	 */
	//public function onPublicationExtended($publication, $option, $miniview=0)
	public function onPublicationSub($publication, $option, $miniview=0)
	{
		$arr = array(
			'html'    => '',
			'metadata'=> '',
			'name'    => 'forks'
		);

		// Make sure forks are allowed
		if (!$publication->config('forks'))
		{
			return $arr;
		}

		// Make sure this plugin is enabled for this category
		if (!$publication->category()->_params->get('plg_forks', 1))
		{
			return $arr;
		}

		// Make sure the license applied allows for derivations
		if ($publication->license() && !$publication->license()->derivatives)
		{
			return $arr;
		}

		$db = App::get('db');

		$objV = new \Components\Publications\Tables\Version($db);
		$versions = $objV->getVersions($publication->id, $filters = array('public' => 1));

		$sources = array();
		foreach ($versions as $version)
		{
			$sources[] = $version->id;
		}

		$forks = 0;
		if (count($sources))
		{
			$db->setQuery("SELECT COUNT(id) FROM `#__publication_versions` WHERE `forked_from` IN (" . implode(',', $sources) . ")");

			$forks = $db->loadResult();
		}

		// Return the output
		$arr['html'] = $this->view('status', 'forks')
			->set('publication', $publication)
			->set('forks', $forks)
			->loadTemplate();

		return $arr;
	}

	/**
	 * Show fork form
	 *
	 * @param   object  $publication
	 * @return  string  HTML
	 */
	private function _forks($publication)
	{
		/* @TODO: Move query to use ORM model
		$forks = Version::all()
			->including('publication')
			->whereEquals('forked_from', $publication->get('id'))
			->whereEquals('state', Publication::STATE_PUBLISHED)
			->where('access', '!=', 2)
			->where('published_up', '<=', Date::toSql(), 1)
				->orWhere('published_up', 'IS', null, 1)
				->orWhere('published_up', '=', '0000-00-00 00:00:00', 1)
				->resetDepth()
			->where('published_up', 'IS', null, 'and', 1)
				->orWhere('published_up', '=', '0000-00-00 00:00:00', 1)
				->orWhere('published_down', '>=', Date::toSql(), 1)
				->resetDepth()
			->rows();
		*/

		// Get a list of all published versions
		$db = App::get('db');

		$objV = new \Components\Publications\Tables\Version($db);
		$versions = $objV->getVersions($publication->id, $filters = array('public' => 1));

		$forks = array();
		foreach ($versions as $version)
		{
			// Now find all the forks for each version
			$db->setQuery("SELECT id, publication_id FROM `#__publication_versions` WHERE `forked_from`=" . $db->quote($version->id) . " ORDER BY `created` DESC");

			$forked = $db->loadObjectList();

			foreach ($forked as $i => $fork)
			{
				$forks[$i] = new Components\Publications\Models\Publication($fork->publication_id, 'default', $fork->id);
				$forks[$i]->set('forked_from', $version->version_label);
				$forks[$i]->set('forked_version', $version->version_number);
			}
		}

		// Instantiate a view
		$view = $this->view('browse', 'forks')
			->set('publication', $publication)
			->set('config', $publication->config())
			->set('forks', $forks)
			->setErrors($this->getErrors());

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Show fork form
	 *
	 * @param   object  $publication
	 * @return  string  HTML
	 */
	private function _fork($publication)
	{
		// Load classes
		require_once Component::path('com_projects') . DS . 'models' . DS . 'project.php';

		// Model
		$model = new Components\Projects\Models\Project();

		// Set filters
		$filters = array(
			'mine'     => 1,
			'updates'  => 1,
			'getowner' => 1,
			'sortby'   => 'title',
			'sortdir'  => 'ASC',
			'filterby' => 'active',
			'uid'      => User::get('id')
		);

		$projects = array();

		// Get owned
		$filters['which'] = 'owned';
		$owned = $model->entries('list', $filters);

		// Push the projects to the primary list
		//
		// Note: we do it this way so we can combine with
		// the "other" list and sort everything by title
		foreach ($owned as $own)
		{
			// If not a  1 = manager, 0 = collaborator, or 2 = author, 5 = reviewer, skip
			if (!in_array($own->get('role'), array(0, 1, 2, 3)))
			{
				continue;
			}
			$projects[$own->get('title')] = $own;
		}

		// Get other projects
		$filters['which'] = 'other';
		$other = $model->entries('list', $filters);

		foreach ($other as $own)
		{
			// If not a  1 = manager, 0 = collaborator, or 2 = author, 5 = reviewer, skip
			if (!in_array($own->get('role'), array(0, 1, 2, 3)))
			{
				continue;
			}
			$projects[$own->get('title')] = $own;
		}

		ksort($projects);

		// Return the output
		return $this->view('fork', 'forks')
			->set('publication', $publication)
			->set('projects', $projects)
			->loadTemplate();
	}
}
