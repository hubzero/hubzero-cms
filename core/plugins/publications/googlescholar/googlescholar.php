<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding Google Scholar metadata to the document
 */
class plgPublicationsGooglescholar extends \Hubzero\Plugin\Plugin
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
		Document::setMetaData('citation_title', $view->escape($publication->title));

		$nullDate = '0000-00-00 00:00:00';

		$thedate = $publication->publish_up;

		if (!$thedate || $thedate == $nullDate)
		{
			$thedate = $publication->accepted;
		}
		if (!$thedate || $thedate == $nullDate)
		{
			$thedate = $publication->submitted;
		}
		if (!$thedate || $thedate == $nullDate)
		{
			$thedate = $publication->created;
		}
		if ($thedate && $thedate != $nullDate)
		{
			Document::setMetaData('citation_date', Date::of($thedate)->toLocal('Y-m-d'));
		}

		if ($doi = $publication->version->get('doi'))
		{
			Document::setMetaData('citation_doi', $view->escape($doi));
		}

		foreach ($publication->_authors as $contributor)
		{
			if ($contributor->role && strtolower($contributor->role) == 'submitter')
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
			$contributor->organization = stripslashes(trim($contributor->organization ? $contributor->organization : ''));

			Document::setMetaData('citation_author', $view->escape($name));

			if ($contributor->organization)
			{
				Document::setMetaData('citation_author_institution', $view->escape($contributor->organization));
			}
		}
	}
}
