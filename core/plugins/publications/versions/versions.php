<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for versions
 */
class plgPublicationsVersions extends \Hubzero\Plugin\Plugin
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

		if ($publication->_category->_params->get('plg_versions'))
		{
			$areas['versions'] = Lang::txt('PLG_PUBLICATION_VERSIONS');
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
	 * @param   boolean  $authorized
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true, $authorized = false)
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
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

		if ($rtrn == 'all' || $rtrn == 'html')
		{
			// Get pub configs
			$config = Component::params($option);

			$database = App::get('db');
			$objV = new \Components\Publications\Tables\Version($database);
			$versions = $objV->getVersions($publication->id, $filters = array('public' => 1));

			// Are we allowing contributions
			$contributable = Plugin::isEnabled('projects', 'publications') ? 1 : 0;

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('option', $option)
				->set('publication', $publication)
				->set('versions', $versions)
				->set('config', $config)
				->set('authorized', $authorized)
				->set('contributable', $contributable);

			// Return the output
			$arr['html'] = $view
				->setErrors($this->getErrors())
				->loadTemplate();
		}

		return $arr;
	}
}
