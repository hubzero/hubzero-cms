<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for adding Dublin Core metadata to the document
 */
class plgResourcesDublincore extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $model   Current model
	 * @param   string  $option  Name of the component
	 * @param   array   $areas   Active area(s)
	 * @param   string  $rtrn    Data to be returned
	 * @return  void
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		if (!App::isSite())
		{
			return;
		}

		if (Request::getWord('tmpl') || Request::getWord('format') || Request::getInt('no_html'))
		{
			return;
		}

		$view = $this->view();

		// Add metadata
		Document::setMetaData('dc.title', $view->escape($model->title));

		$thedate = $model->date;

		if ($thedate)
		{
			Document::setMetaData('dc.date', Date::of($thedate)->toLocal('Y-m-d'));
		}

		if ($model->isTool())
		{
			$tconfig = Component::params('com_tools');

			if ($model->doi && $tconfig->get('doi_shoulder'))
			{
				$doi = $tconfig->get('doi_shoulder') . '/' . strtoupper($model->doi);
			}
			else
			{
				$doi = '10254/' . $tconfig->get('doi_prefix') . $model->id . '.' . $model->doi_label;
			}

			Document::setMetaData('dc.identifier', $view->escape($doi));
		}

		Document::setMetaData('dcterms.description', $view->escape($model->introtext));

		if ($license = $model->params->get('license', ''))
		{
			Document::setMetaData('dcterms.license', $view->escape($license));
		}

		foreach ($model->contributors('!submitter') as $contributor)
		{
			if (strtolower($contributor->role) == 'submitter')
			{
				continue;
			}

			$name = $this->name($contributor);

			if (!$contributor->org)
			{
				$contributor->org = $contributor->xorg;
			}
			$contributor->org = stripslashes(trim($contributor->org));

			Document::setMetaData('dcterms.creator', $view->escape($name . ($contributor->org ? ', ' . $contributor->org : '')));
		}
	}

	/**
	 * Return contributor name
	 *
	 * @param   object  $contributor
	 * @return  string
	 */
	private function name($contributor)
	{
		$name = '';

		if ($contributor->name)
		{
			$name = stripslashes($contributor->name);
		}
		else if ($contributor->surname || $contributor->givenName)
		{
			$name = stripslashes($contributor->givenName) . ' ';
			if ($contributor->middleName != null)
			{
				$name .= stripslashes($contributor->middleName) . ' ';
			}
			$name .= stripslashes($contributor->surname);
		}
		else
		{
			$name = stripslashes($contributor->xname);
		}

		return $name;
	}
}
