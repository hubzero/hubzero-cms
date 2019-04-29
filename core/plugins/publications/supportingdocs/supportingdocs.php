<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for supporting docs
 */
class plgPublicationsSupportingDocs extends \Hubzero\Plugin\Plugin
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

		if ($publication->_category->_params->get('plg_supportingdocs'))
		{
			$areas['supportingdocs'] = Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS');
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
	 * @param   boolean  $authorized   Whether or not authorized
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true, $authorized = true)
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
				// do nothing
				return $arr;
			}
		}

		if (!$publication->_category->_params->get('plg_supportingdocs'))
		{
			return $arr;
		}

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$database = App::get('db');

			$config = Component::params($option);

			// Get docs
			$pContent = new \Components\Publications\Tables\Attachment($database);
			$docs = $pContent->getAttachments(
				$publication->version_id,
				array(
					'role'  => array(1, 0, 2),
					'order' => 'a.role DESC, a.ordering ASC'
				)
			);

			// Get projects html helper
			require_once Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';

			// Build publication path
			$path = \Components\Publications\Helpers\Html::buildPubPath(
				$publication->id,
				$publication->version_id,
				$config->get('webpath'),
				$publication->secret,
				$root = 1
			);

			// Pass the view some info

			$view = $this->view('default', 'browse')
				->set('option', $option)
				->set('publication', $publication)
				->set('config', $config)
				->set('version', $version)
				->set('authorized', $authorized)
				->set('path', $path)
				->set('docs', $docs)
				->set('live_site', rtrim(Request::base(), '/'));

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}
}
