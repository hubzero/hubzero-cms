<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for adding Google Scholar metadata to the document
 */
class plgResourcesGooglescholar extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $resource  Current resource
	 * @param   string  $option    Name of the component
	 * @param   array   $areas     Active area(s)
	 * @param   string  $rtrn      Data to be returned
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
		Document::setMetaData('citation_title', $view->escape($model->resource->title));

		switch ($model->params->get('show_date'))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = $model->resource->created;    break;
			case 2: $thedate = $model->resource->modified;   break;
			case 3: $thedate = $model->resource->publish_up; break;
		}
		if ($thedate)
		{
			Document::setMetaData('citation_date', Date::of($thedate)->toLocal('M d, Y'));
		}

		if ($model->isTool())
		{
			$tconfig = Component::params('com_tools');

			if ($model->resource->doi && $tconfig->get('doi_shoulder'))
			{
				$doi = $tconfig->get('doi_shoulder') . '/' . strtoupper($model->resource->doi);
			}
			else
			{
				$doi = '10254/' . $tconfig->get('doi_prefix') . $model->resource->id . '.' . $model->resource->doi_label;
			}

			Document::setMetaData('citation_doi', $view->escape($doi));
		}

		foreach ($model->contributors('!submitter') as $contributor)
		{
			if (strtolower($contributor->role) == 'submitter')
			{
				continue;
			}

			if ($contributor->name)
			{
				$name = stripslashes($contributor->name);
			}
			else if ($contributor->surname || $contributor->givenName)
			{
				$name = stripslashes($contributor->givenName) . ' ';
				if ($contributor->middleName != NULL)
				{
					$name .= stripslashes($contributor->middleName) . ' ';
				}
				$name .= stripslashes($contributor->surname);
			}
			else
			{
				$name = stripslashes($contributor->xname);
			}

			if (!$contributor->org)
			{
				$contributor->org = $contributor->xorg;
			}
			$contributor->org = stripslashes(trim($contributor->org));

			Document::setMetaData('citation_author', $view->escape($name));
			if ($contributor->org)
			{
				Document::setMetaData('citation_author_institution', $view->escape($contributor->org));
			}
		}
	}
}
