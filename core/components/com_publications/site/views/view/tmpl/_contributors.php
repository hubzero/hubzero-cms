<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = \App::get('db');

if ($this->contributors)
{
	$html 		= '';
	$names 		= array();
	$orgs 		= array();
	$i 			= 1;
	$k 			= 0;
	$orgsln 	= '';
	$names_s 	= array();
	$orgsln_s 	= '';

	foreach ($this->contributors as $contributor)
	{
		if ($this->incSubmitter == false && $contributor->role == 'submitter')
		{
			continue;
		}

		// Build the user's name and link to their profile
		if ($contributor->name)
		{
			$name = $this->escape(stripslashes($contributor->name));
		}
		else
		{
			$name = $this->escape(stripslashes($contributor->p_name));
		}
		if ($this->format)
		{
			$nameParts    = explode(" ", $name);
			$name = end($nameParts);
			$name.= count($nameParts) > 1 ? ', ' . strtoupper(substr($nameParts[0], 0, 1)) . '.' : '';
			$name.= count($nameParts) > 2 ? ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.' : '';
		}

		if (!$contributor->organization)
		{
			$contributor->organization = $contributor->p_organization;
		}
		$contributor->organization = $this->escape(stripslashes(trim($contributor->organization)));

		$name = str_replace( '"', '&quot;', $name );
		if ($contributor->user_id && $contributor->open)
		{
			$link  = '<a href="' . Route::url('index.php?option=com_members&amp;id=' . $contributor->user_id)
					. '" title="View the profile of ' . $name . '">' . $name . '</a>';
		}
		else
		{
			$link = $name;
		}
		$link .= ($contributor->role) ? ' ('.$contributor->role.')' : '';

		if (trim($contributor->organization) != '' && !in_array(trim($contributor->organization), $orgs))
		{
			$orgs[$i-1] = trim($contributor->organization);
			$orgsln 	.= $i. '. ' . trim($contributor->organization) . ' ';
			$orgsln_s 	.= trim($contributor->organization) . ' ';
			$k = $i;
			$i++;
		}
		else if (trim($contributor->organization) != '')
		{
			$k = array_search(trim($contributor->organization), $orgs) + 1;
		}
		else
		{
			$k = 0;
		}

		$link_s = $link;
		if ($this->showorgs && $k)
		{
			$link .= '<sup>' . $k . '</sup>';
		}
		if ($contributor->orcid)
		{
			$orcid = '<a href="https://orcid.org/' . $contributor->orcid . '" target="blank" title="' . $name . '\'s ORCID page"><img alt="ORCID logo" src="https://info.orcid.org/wp-content/uploads/2019/11/orcid_16x16.png" width="16" height="16" /></a>';
			$link_s .= $orcid;
			$link .= $orcid;
		}
		$names_s[] = $link_s;
		$names[] = $link;
	}

	if (count($names) > 0)
	{
		$html = '<p>' . ucfirst(Lang::txt('By')) . ' ';
		$html .= count($names) > 1 && count($orgs) > 0  ? implode( ', ', $names ) : implode( ', ', $names_s );
		$html .= '</p>';
	}
	if ($this->showorgs && count($orgs) > 0)
	{
		$html .= '<p class="orgs">';
		$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
		$html .= '</p>';
	}
	if ($this->showaslist)
	{
		$html = count($names) > 1  ? implode( ', ', $names ) : implode( ', ', $names_s );
	}

}
else
{
	$html = '';
}

echo $html;
