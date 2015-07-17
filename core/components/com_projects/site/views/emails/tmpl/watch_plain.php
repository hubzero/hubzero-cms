<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$sef  = Route::url($this->project->link('alias'));
$link = rtrim($base, '/') . '/' . trim($sef, '/');

$message  = $this->subject . "\n";
$message .= '===============================' . "\n";
$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias') . ')' . "\n";
$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $link . "\n";
$message .= '===============================' . "\n\n";

if (empty($this->activities))
{
	$message .= Lang::txt('There has been no activity in this project.');
}
else
{
	foreach ($this->activities as $a)
	{
		$body     = NULL;
		$activity = $a->activity;

		switch ($a->class)
		{
			case 'quote':
				// Get comment
				$objC  = $this->project->table('Comment');
				if ($objC->loadComment($a->referenceid))
				{
					$body = stripslashes($objC->comment);
					$body = str_replace('<!-- {FORMAT:HTML} -->', '', $body);
				}

				break;

			case 'blog':
				$activity = 'posted a status update';
				$objM     = $this->project->table('Blog');
				$blog     = $objM->getEntries(
					$a->projectid,
					$bfilters = array('activityid' => $a->id),
					$a->referenceid
				);
				$body = $blog && !empty($blog[0]) ? preg_replace("/\n/", '<br />', trim($blog[0]->blogentry)) : NULL;
				break;

			case 'todo':
				$objTD = $this->project->table('Todo');
				$todo = $objTD->getTodos(
					$a->projectid,
					$tfilters = array('activityid' => $a->id),
					$a->referenceid
				);
				if ($todo && !empty($todo[0]))
				{
					$body = $todo[0]->details ? $todo[0]->details : $todo[0]->content;
				}
				break;
		}
		$message .= Date::of($a->recorded)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
					. ' &#64 ' .  Date::of($a->recorded)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . "\n";
		$message .= $a->admin == 1 ? Lang::txt('Administrator') : $a->name;
		$message .= ' ' . $activity;
		$message .= $body ? ':' : '';
		$message .= "\n";
		if ($body)
		{
			$message .= $body . "\n";
		}
		$message .= '-------------------------------' . "\n";
	}
}
echo $message;
?>

This email was sent to you on behalf of <?php echo Request::root(); ?> because you are subscribed
to watch this project. To unsubscribe, go to <?php echo $link; ?>.