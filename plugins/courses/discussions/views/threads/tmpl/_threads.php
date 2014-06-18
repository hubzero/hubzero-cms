<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$view = $this->view('_thread');
		$view->set('option', $this->option)
		     ->set('course', $this->course)
		     ->set('unit', $this->unit)
		     ->set('lecture', $this->lecture)
		     ->set('config', $this->config)
		     ->set('thread', $thread)
		     ->set('cls', $cls)
		     ->set('base', $this->base)
		     ->set('search', $this->search)
		     ->set('active', (isset($this->active) ? $this->active : ''));

		if (!$thread->scope_sub_id)
		{
			$subs[] = $thread->id;
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
		$offering = CoursesModelOffering::getInstance(JRequest::getVar('offering', ''));
		if ($offering->exists())
		{
			$database = JFactory::getDBO();
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
		<p><?php echo JText::_('No discussions found.'); ?></p>
	</li>
<?php
}
?>
</ul>