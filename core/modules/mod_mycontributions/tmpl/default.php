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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

// Build the HTML
$html  = '';
$html .= "\t\t" . '<ul class="module-nav"><li><a class="icon-plus" href="' . Route::url('index.php?option=com_resources&task=draft') . '">' . Lang::txt('MOD_MYCONTRIBUTIONS_START_NEW') . '</a></li></ul>' . "\n";

$tools = $this->tools;
if ($this->show_tools && $tools)
{
	$html .= '<h4>';

	$html .= '<a href="' . Route::url('index.php?option=com_tools&controller=pipeline&task=pipeline') . '">' . Lang::txt('MOD_MYCONTRIBUTIONS_TOOLS') . ' ';
	if (count($tools) > $this->limit_tools)
	{
		$html .= '<span>' . Lang::txt('MOD_MYCONTRIBUTIONS_VIEW_ALL') . ' ' . count($tools) . '</span>';
	}
	$html .= '</a></h4>' . "\n";

	$html .= '<ul class="expandedlist">' . "\n";
	for ($i=0; $i < count($tools); $i++)
	{
		if ($i <= $this->limit_tools)
		{
			$class =  $tools[$i]->published ? 'published' : 'draft';
			$urgency = ($this->getState($tools[$i]->state) == 'installed' or $this->getState($tools[$i]->state)=='created') ? ' ' . Lang::txt('MOD_MYCONTRIBUTIONS_ACTION_REQUIRED') : '' ;

			$html .= '<li class="' . $class . '">' . "\n";
			$html .= '<a href="' . Route::url('index.php?option=com_tools&controller=pipeline&task=status&app=' . $tools[$i]->toolname) . '">' . stripslashes($tools[$i]->toolname) . '</a>' . "\n";
			$html .= '<span class="under">' . Lang::txt('MOD_MYCONTRIBUTIONS_STATUS') . ': <span class="status_' . $this->getState($tools[$i]->state) . '"><a href="' . Route::url('index.php?option=com_tools&controller=pipeline&task=status&app=' . $tools[$i]->toolname) . '" title="' . Lang::txt('MOD_MYCONTRIBUTIONS_TOOL_STATUS', $this->getState($tools[$i]->state), $urgency) . '">' . $this->getState($tools[$i]->state) . '</a></span>' . "\n";
			if ($tools[$i]->published)
			{
				$html .= '<span class="extra">' . "\n";
				$html .= (!$this->show_wishes) ? '<span class="item_empty ">&nbsp;</span>' : '';
				$html .= (!$this->show_tickets) ? '<span class="item_empty ">&nbsp;</span>' : '';
				if ($this->show_questions)
				{
					$html .= '<span class="item_q">';
					$html .= '<a href="' . Route::url('index.php?option=com_resources&id=' . $tools[$i]->rid . '&active=answers') . '" title="' . Lang::txt('MOD_MYCONTRIBUTIONS_NUM_QUESTION' . ($tools[$i]->q > 1 ? 'S' : ''), $tools[$i]->q, $tools[$i]->q_new) . '">' . $tools[$i]->q . '</a>';
					$html .= '</span>' . "\n";
				}
				else
				{
					$html .= '<span class="item_empty">&nbsp;</span>';
				}
				if ($this->show_wishes)
				{
					$html .= '<span class="item_w">';
					$html .= '<a href="' . Route::url('index.php?option=com_resources&id=' . $tools[$i]->rid . '&active=wishlist') . '" title="' . Lang::txt('MOD_MYCONTRIBUTIONS_NUM_WISH' . ($tools[$i]->w > 1 ? 'S' : ''), $tools[$i]->w, $tools[$i]->w_new) . '">' . $tools[$i]->w . '</a>';
					$html .='</span>' . "\n";
				}
				if ($this->show_tickets)
				{
					$html .= '<span class="item_s">';
					$html .= '<a href="' . Route::url('index.php?option=com_support&task=tickets&find=group:' . $tools[$i]->devgroup) . '" title="' . Lang::txt('MOD_MYCONTRIBUTIONS_NUM_TICKET' . ($tools[$i]->s > 1 ? 'S' : ''), $tools[$i]->s, $tools[$i]->s_new) . '">' . $tools[$i]->s . '</a>';
					$html .= '</span>' . "\n";
				}
				$html .= '</span>' . "\n";
			}
			$html .= '</span>' . "\n";
			$html .= '</li>' . "\n";
		}
	}
	$html .= '</ul>' . "\n";

	$html .= '<h4><a href="' . Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=contributions') . '">' . Lang::txt('MOD_MYCONTRIBUTIONS_OTHERS_IN_PROGRESS');
	if ($this->contributions && count($this->contributions) > $this->limit_other)
	{
		$html .= '<span>' . Lang::txt('MOD_MYCONTRIBUTIONS_VIEW_ALL') . '</span>' . "\n";
	}
	$html .= '</a></h4>' . "\n";
}

$contributions = $this->contributions;
if (!$contributions)
{
	$html .= '<p>' . Lang::txt('MOD_MYCONTRIBUTIONS_NONE_FOUND') . '</p>' . "\n";
}
else
{
	require_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

	$html .= '<ul class="expandedlist">' . "\n";
	for ($i=0; $i < count($contributions); $i++)
	{
		if ($i < $this->limit_other)
		{
			// Determine css class
			switch ($contributions[$i]->published)
			{
				case 1:  $class = 'published';  break;  // published
				case 2:  $class = 'draft';      break;  // draft
				case 3:  $class = 'pending';    break;  // pending
			}

			// Get author login
			$author_login = Lang::txt('MOD_MYCONTRIBUTIONS_UNKNOWN');
			$author = Components\Members\Models\Member::oneOrNew($contributions[$i]->created_by);
			if ($author->get('id'))
			{
				$author_login = stripslashes($author->get('name'));
				if (in_array($author->get('access'), User::getAuthorisedViewLevels()))
				{
					$author_login = '<a href="' . Route::url($author->link()) . '">' . $author_login . '</a>';
				}
			}

			$html .= "\t" . '<li class="' . $class . '">' . "\n";
			$html .= "\t\t" . '<a href="' . Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $contributions[$i]->id) . '">' . \Hubzero\Utility\String::truncate(stripslashes($contributions[$i]->title), 40) . '</a>' . "\n";
			$html .= "\t\t" . '<span class="under">' . Lang::txt('MOD_MYCONTRIBUTIONS_TYPE') . ': ' . $contributions[$i]->typetitle . '<br />' . Lang::txt('MOD_MYCONTRIBUTIONS_SUBMITTED_BY', $author_login) . '</span>' . "\n";
			$html .= "\t" . '</li>' . "\n";
		}
	}
	$html .= '</ul>' . "\n";
}

// Output final HTML
echo $html;
