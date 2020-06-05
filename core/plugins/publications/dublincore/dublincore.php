<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding Dublin Core metadata to the document
 */
class plgPublicationsDublincore extends \Hubzero\Plugin\Plugin
{
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
		if (!App::isSite()
		 || Request::getWord('format') == 'raw'
		 || Request::getInt('no_html'))
		{
			return;
		}

		$view = $this->view();

		$publication->authors();
		$publication->license();

		// Add metadata
		Document::setMetaData('dc.title', $view->escape($publication->title));

		$nullDate = '0000-00-00 00:00:00';

		if ($publication->publish_up && $publication->publish_up != $nullDate)
		{
			Document::setMetaData('dc.date', Date::of($publication->publish_up)->toLocal('Y-m-d'));
		}
		if ($publication->submitted && $publication->submitted != $nullDate)
		{
			Document::setMetaData('dc.date.submitted', Date::of($publication->submitted)->toLocal('Y-m-d'));
		}
		if ($publication->accepted && $publication->accepted != $nullDate)
		{
			Document::setMetaData('dc.date.approved', Date::of($publication->accepted)->toLocal('Y-m-d'));
		}

		if ($doi = $publication->version->get('doi'))
		{
			Document::setMetaData('dc.identifier', $view->escape($doi));
		}

		Document::setMetaData('dcterms.description', $view->escape($publication->abstract));

		$license = $publication->license();
		if (is_object($license))
		{
			Document::setMetaData('dcterms.license', $view->escape($license->title));
		}

		foreach ($publication->_authors as $contributor)
		{
			if (strtolower($contributor->role) == 'submitter')
			{
				continue;
			}

			if ($contributor->name)
			{
				$name = stripslashes($contributor->name);
			}
			else
			{
				$name = stripslashes($contributor->p_name);
			}

			if (!$contributor->organization)
			{
				$contributor->organization = $contributor->p_organization;
			}
			$contributor->organization = stripslashes(trim($contributor->organization));

			Document::setMetaData('dcterms.creator', $view->escape($name . ($contributor->organization ? ', ' . $contributor->organization : '')));
		}
	}
}
