<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$database = App::get('db');

if ($this->contributors)
{
	$html = '';
	$names = array();
	$orgs = array();
	$i = 1;
	$k = 0;
	$orgsln = '';
	$names_s = array();
	$orgsln_s = '';

	foreach ($this->contributors as $contributor)
	{
		if (strtolower($contributor->role) == 'submitter')
		{
			continue;
		}

		// Build the user's name and link to their profile
		if ($contributor->name)
		{
			$name = $this->escape(stripslashes($contributor->name));
		}
		else if ($contributor->surname || $contributor->givenName)
		{
			$name = $this->escape(stripslashes($contributor->givenName)) . ' ';
			if ($contributor->middleName != null)
			{
				$name .= $this->escape(stripslashes($contributor->middleName)) . ' ';
			}
			$name .= $this->escape(stripslashes($contributor->surname));
		}
		else
		{
			$name = $this->escape(stripslashes($contributor->xname));
		}
		if (!trim($name))
		{
			$name = Lang::txt('(unknown)');
		}

		/*if (!$contributor->organization)
		{
			$contributor->org = $contributor->xorg;
		}*/
		$contributor->organization = $this->escape(stripslashes(trim($contributor->organization)));

		if (!isset($contributor->authorid) && isset($contributor->uid))
		{
			$contributor->authorid = $contributor->uid;
		}

		$link = $name;
		if ($contributor->authorid)
		{
			$profile = User::getInstance($contributor->authorid);
			if ($profile->get('id') && in_array($profile->get('access'), User::getAuthorisedViewLevels()))
			{
				$link = '<a href="' . Route::url($profile->link()) . '" rel="contributor" title="' . Lang::txt('COM_RESOURCES_VIEW_MEMBER_PROFILE', $name) . '">' . $name . '</a>';
			}
		}

		$link .= ($contributor->role) ? ' (' . $contributor->role . ')' : '';

		if (trim($contributor->organization) != '' && !in_array(trim($contributor->organization), $orgs))
		{
			$orgs[$i-1] = trim($contributor->organization);
			$orgsln    .= $i . '. ' . trim($contributor->organization) . ' ';
			$orgsln_s  .= trim($contributor->organization).' ';
			$k = $i;
			$i++;
		}
		else
		{
			$k = array_search(trim($contributor->organization), $orgs) + 1;
		}
		$link_s = $link;
		if (trim($contributor->organization) != '')
		{
			$link .= '<sup>' . $k . '</sup>';
		}

		$names_s[] = $link_s;
		$names[] = $link;
	}

	if (count($names) > 0)
	{
		$html = '<p>' . Lang::txt('COM_RESOURCES_BY_AUTHORS', (count($this->contributors) > 1 ? implode(', ', $names) : implode(', ', $names_s))) . '</p>';
	}

	if (count($orgs) > 0)
	{
		$html .= '<p class="orgs">';
		$html .= count($this->contributors) > 1 ? $orgsln : $orgsln_s;
		$html .= '</p>';
	}
}
else
{
	$html = '';
}

echo $html;
