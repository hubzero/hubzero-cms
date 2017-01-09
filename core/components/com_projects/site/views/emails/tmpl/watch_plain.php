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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
				$body = $blog && !empty($blog[0]) ? trim($blog[0]->blogentry) : '';
				if (!preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $body))
				{
					$body = preg_replace("/\n/", '<br />', $body);
				}
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