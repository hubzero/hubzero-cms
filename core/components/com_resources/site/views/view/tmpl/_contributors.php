<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$database = App::get('db');

if ($this->contributors)
{
	require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

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
			if ($contributor->middleName != NULL)
			{
				$name .= $this->escape(stripslashes($contributor->middleName)) . ' ';
			}
			$name .= $this->escape(stripslashes($contributor->surname));
		}
		else
		{
			$name = $this->escape(stripslashes($contributor->xname));
		}

		if (!$contributor->org)
		{
			$contributor->org = $contributor->xorg;
		}
		$contributor->org = $this->escape(stripslashes(trim($contributor->org)));

		$link = $name;
		if ($contributor->id)
		{
			$profile = Components\Members\Models\Member::oneOrNew($contributor->id);
			if ($profile->get('id') && in_array($profile->get('access'), User::getAuthorizedViewLevels()))
			{
				$link = '<a href="' . Route::url($profile->link()) . '" rel="contributor" title="' . Lang::txt('COM_RESOURCES_VIEW_MEMBER_PROFILE', $name) . '">' . $name . '</a>';
			}
		}

		$link .= ($contributor->role) ? ' (' . $contributor->role . ')' : '';

		if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs))
		{
			$orgs[$i-1] = trim($contributor->org);
			$orgsln    .= $i . '. ' . trim($contributor->org) . ' ';
			$orgsln_s  .= trim($contributor->org).' ';
			$k = $i;
			$i++;
		}
		else
		{
			$k = array_search(trim($contributor->org), $orgs) + 1;
		}
		$link_s = $link;
		if (trim($contributor->org) != '')
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