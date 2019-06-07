<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for citations
 */
class plgPublicationsCitations extends \Hubzero\Plugin\Plugin
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
		if ($publication->_category->_params->get('plg_citations'))
		{
			$areas = array(
				'citations' => Lang::txt('PLG_PUBLICATION_CITATIONS')
			);
		}
		else
		{
			$areas = array();
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
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true )
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

		if (!$publication->_category->_params->get('plg_citations'))
		{
			return $arr;
		}

		// Get a needed library
		include_once Component::path('com_citations') . DS . 'models' . DS . 'citation.php';

		// Get citations for this publication
		$c = \Components\Citations\Models\Association::all()
			->whereEquals('tbl', 'publication')
			->whereEquals('oid', $publication->id)
			->including('citation')
			->rows();
		$citations = array();
		foreach ($c as $assoc)
		{
			$citations[] = $assoc->citation;
		}

		$arr['count'] = $citations ? count($citations) : 0;
		$arr['name']  = 'citations';

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$config = Component::params($option);

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('option', $option)
				->set('publication', $publication)
				->set('citations', $citations)
				->set('format', $config->get('citation_format', 'apa'));

			// Return the output
			$arr['html'] = $view
				->setErrors($this->getErrors())
				->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = $this->view('default', 'metadata')
				->set('url', Route::url('index.php?option=' . $option . '&' . ($publication->alias ? 'alias=' . $publication->alias : 'id=' . $publication->id) . '&active=citations&v=' . $publication->version_number))
				->set('citations', $citations);

			$arr['metadata'] = $view->loadTemplate();
		}

		// Return results
		return $arr;
	}
}
