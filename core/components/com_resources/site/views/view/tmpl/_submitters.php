<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$html = '';

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

	$database = App::get('db');

	$types = array(
		'manager'       => 'manager',
		'administrator' => 'administrator',
		'super users'   => 'super administrator',
		'publisher'     => 'publisher',
		'editor'        => 'editor'
	);
	//$types = array(23 => 'manager', 24 => 'administrator', 25 => 'super administrator', 21 => 'publisher', 20 => 'editor');

	foreach ($this->contributors as $contributor)
	{
		if (strtolower($contributor->role) != 'submitter')
		{
			continue;
		}

		// Build the user's name and link to their profile
		if ($contributor->name)
		{
			$name = $contributor->name;
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
			$name = $contributor->xname;
		}
		/*if (!$contributor->org)
		{
			$contributor->org = $contributor->xorg;
		}*/

		$name = $this->escape(stripslashes($name));

		$link = $name;
		if ($contributor->authorid)
		{
			$profile = User::getInstance($contributor->authorid);
			if ($profile->get('id') && in_array($profile->get('access'), User::getAuthorisedViewLevels()))
			{
				$link = '<a href="' . Route::url($profile->link()) . '" rel="contributor" title="' . Lang::txt('COM_RESOURCES_VIEW_MEMBER_PROFILE', $name) . '">' . $name . '</a>';
			}
		}

		//if ($newstyle)
		//{
			if ($this->badges)
			{
				$xuser = User::getInstance($contributor->id);
				if (is_object($xuser) && $xuser->get('name'))
				{
					// get users groups
					// in reverse to get the highest levels first
					$groupIds = Hubzero\Access\Access::getGroupsByUser($xuser->id, false);
					$database->setQuery("SELECT title FROM `#__usergroups` WHERE `id` IN (" . implode(',', $groupIds) . ") ORDER BY lft ASC");
					$groups = array_reverse($database->loadColumn());

					// use the users first group
					$gid = isset($groups[0]) ? strtolower($groups[0]) : null;

					if (isset($types[$gid]))
					{
						$link .= ' <span class="badge">' . str_replace(' ', '-', $types[$gid]) . '</span>';
					}
				}
			}

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
		/*}
		else
		{
			$orgs[trim($contributor->organization)][] = $link;
		}*/

		$names[] = $link;
	}

	if (count($names) > 0)
	{
		$html  = '<p>';
		$html .= count($orgs) > 1  ? implode(', ', $names) : implode(', ', $names_s);
		$html .= '</p>';
	}
	if ($this->showorgs && count($orgs) > 0)
	{
		$html .= '<p class="orgs">';
		$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
		$html .= '</p>';
	}
}

echo $html;
