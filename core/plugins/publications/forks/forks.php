<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

		if ($publication->_category->_params->get('plg_forks') && $extended)
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
		if (!$publication->_category->_params->get('plg_forks') || !$extended)
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
		if (!$publication->license()->derivatives)
		{
			return $arr;
		}

		$db = App::get('db');
		$db->setQuery("SELECT COUNT(id) FROM `#__publication_versions` WHERE `forked_from`=" . $db->quote($publication->version->get('id')));

		$forks = $db->loadResult();

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
			->orWhere('published_up', '=', '0000-00-00 00:00:00', 1)
			->resetDepth()
			->whereRaw('published_up', ' IS NULL', 1)
			->orWhere('published_down', '>=', Date::toSql(), 1)
			->orWhere('published_up', '=', '0000-00-00 00:00:00', 1)
			->resetDepth()
			->rows();
		*/

		$db = App::get('db');
		$db->setQuery("SELECT id, publication_id FROM `#__publication_versions` WHERE `forked_from`=" . $db->quote($publication->version->get('id')) . " ORDER BY `created` DESC");

		$forks = $db->loadObjectList();

		foreach ($forks as $i => $fork)
		{
			$forks[$i] = new Components\Publications\Models\Publication($fork->publication_id, 'default', $fork->id);
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
			// If not a manager, collaborator, or author, skip
			if (!in_array($own->get('role'), array(1, 2, 3)))
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
			// If not a manager, collaborator, or author, skip
			if (!in_array($own->get('role'), array(1, 2, 3)))
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
