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

// No direct access
defined('_HZEXEC_') or die();

$lastchange = '0000-00-00 00:00:00';
if ($this->threads && is_array($this->threads))
{
	$lastchange = $this->threads[0]->created;
}
if (!isset($this->category))
{
	$this->category = 'category' . substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,10);
}
?>
<ul class="discussions" id="<?php echo $this->category; ?>" data-lastchange="<?php echo $lastchange; ?>">
	<?php
	if ($this->threads && is_array($this->threads))
	{
		$cls = 'odd';
		if (isset($this->cls))
		{
			$cls = ($this->cls == 'odd') ? 'even' : 'odd';
		}

		$this->depth++;

		if (!isset($this->search))
		{
			$this->search = '';
		}

		$subs = array();
		foreach ($this->threads as $thread)
		{
			$view = $this->view('_thread')
				->set('option', $this->option)
				->set('course', $this->course)
				->set('unit', $this->unit)
				->set('lecture', $this->lecture)
				->set('config', $this->config)
				->set('thread', $thread)
				->set('cls', $cls)
				->set('base', $this->base)
				->set('search', $this->search)
				->set('active', (isset($this->active) ? $this->active : ''));

			if (!$thread->get('scope_sub_id'))
			{
				$subs[] = $thread->get('id');
			}

			if (isset($this->instructors))
			{
				$view->set('instructors', $this->instructors);
			}
			if (isset($this->prfx))
			{
				$view->set('prfx', $this->prfx);
			}

			$view->display();
		}

		if (count($subs) > 0)
		{
			$offering = \Components\Courses\Models\Offering::getInstance(Request::getVar('offering', ''));
			if ($offering->exists())
			{
				$database = App::get('db');
				$database->setQuery("UPDATE `#__forum_posts` SET scope_sub_id=" . $offering->section()->get('id') . " WHERE scope='course' AND scope_sub_id=0 AND id IN(" . implode(",", $subs) . ")");
				if (!$database->query())
				{
					echo '<!-- Failed to update data -->';
				}
			}
		}
	} else {
	?>
		<li class="comments-none">
			<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NO_TOPICS_FOUND'); ?></p>
		</li>
	<?php
	}
	?>
</ul>