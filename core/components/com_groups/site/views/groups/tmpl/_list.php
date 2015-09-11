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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$num_columns = $this->get('num_columns', 2);
$display_logos = $this->get('display_logos', true);
$display_private_description = $this->get('display_private_description', false);
$description_char_limit = $this->get('description_char_limit', 150);

//check to see if we have any groups to show
if (!$this->groups)
{
	echo '<p class="info">' . Lang::txt('COM_GROUPS_INTRO_NO_' . str_replace(' ', '_', strtoupper($this->name)), User::get('id')) . '</p>';
}
else
{
	//var to hold html
	$html = '<div class="grid">';

	//var to hold count
	$count = 1;
	$totalCount = 0;

	//get current user object
	$user = \Hubzero\User::getInstance();

	//loop through each group
	foreach ($this->groups as $group)
	{
		//get the Hubzero Group Object
		$hg = \Hubzero\User\Group::getInstance($group->gidNumber);

		$gt = new \Components\Groups\Models\Tags();

		//var to hold group description
		$description = '';

		//get the column were on
		$cls = '';
		if ($count == $num_columns)
		{
			$count = 0;
			$cls = 'omega';
		}

		//how many columns are we showing
		switch ($num_columns)
		{
			case 2: $columns = 'span6'; break;
			case 3: $columns = 'span4'; break;
			case 4: $columns = 'span3'; break;
		}

		//if we want to display private description and if we have a private description
		if ($display_private_description && $hg->private_desc)
		{
			$description = $hg->getDescription('parsed', 0, 'private');
		}
		elseif ($hg->public_desc)
		{
			$description = $hg->getDescription('parsed', 0, 'public');
		}

		//are we a group manager
		$isManager = (in_array($user->get('id'), $hg->get('managers'))) ? true : false;

		//are we publised
		$isPublished = ($hg->get('published')) ? true : false;

		//if we have a description then strip tags, remove links, and shorten
		if ($description != '')
		{
			$description = strip_tags($description);
			$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
			$description = preg_replace("/$UrlPtrn/", '', $description);
		}
		else
		{
			$description = '<em>No group description available.</em>';
		}

		//shorten description
		$gdescription  = substr($description, 0, $description_char_limit);
		$gdescription .= (strlen($description) > $description_char_limit && $description_char_limit != 0) ? "&hellip;" : "";

		//get the group logo
		$logo = $hg->getLogo();

		//build the html
		$html .= "<div class=\"{$columns} col {$cls}\">";
			$html .= '<div class="group-list">';
				if ($display_logos)
				{
					$html .= "<div class=\"logo\"><img src=\"{$logo}\" alt=\"{$hg->description}\" /></div>";
					$d_cls = '-w-logo';
				}
				else
				{
					$d_cls = "";
				}
				$html .= '<div class="details' . $d_cls . '">';
					if (!$isPublished)
					{
						$html .= '<h3>' . $hg->description . '</h3>';
					}
					else
					{
						$html .= '<h3><a href="' . Route::url('index.php?option=com_groups&task=view&cn=' . $group->cn) . '">' . $hg->description . '</a></h3>';
					}

					if ($gdescription)
					{
						$html .= '<p>' . $gdescription . '</p>';
					}
					if ($isManager)
					{
						$html .= '<span class="status manager">Manager</span>';
					}
					if (!$isPublished)
					{
						$html .= '<span class="status not-published">' . Lang::txt('Group has been unpublished by administrator') . '</span>';
					}

					if (isset($group->matches))
					{
						$html .= '<ol class="tags">';
						foreach ($group->matches as $t)
						{
							$html .= '<li><a href="' . Route::url($gt->tag($t)->link()) . '">' . $t . '</a></li>';
						}
						$html .= '</ol>';
					}
				$html .= "</div>";
			$html .= "</div>";
		$html .= "</div>";

		//increment counter
		$count++;
		$totalCount++;
	}

	$html .= '</div>';

	echo $html;
}