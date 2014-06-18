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

if ($this->depth == 0 && $this->config->get('access-edit-thread'))
{
	$stick = $this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '&thread=' . $this->post->thread . '&action=sticky&sticky=';
?>
<div class="sticky-thread-controls<?php echo ($this->post->sticky) ? ' stuck' : ''; ?>" data-thread="<?php echo $this->post->thread; ?>">
	<p>
		<a class="sticky-toggle"
			href="<?php echo JRoute::_($stick . ($this->post->sticky ? 0 : 1)); ?>"
			data-stick-href="<?php echo JRoute::_($stick . '1'); ?>"
			data-unstick-href="<?php echo JRoute::_($stick . '0'); ?>"
			data-stick-txt="<?php echo JText::_('Make sticky'); ?>"
			data-unstick-txt="<?php echo JText::_('Make not sticky'); ?>">
			<?php echo ($this->post->sticky) ? JText::_('Make not sticky') : JText::_('Make sticky'); ?>
		</a>
		<span class="hint">
			<?php echo JText::_('Sticky discussions are viewable by all sections'); ?>
		</span>
	</p>
</div>
<?php
}
?>
<ol class="comments" id="t<?php echo $this->parent; ?>">
<?php
if ($this->comments && is_array($this->comments))
{
	$cls = 'odd';
	if (isset($this->cls))
	{
		$cls = ($this->cls == 'odd') ? 'even' : 'odd';
	}

	if (!isset($this->search))
	{
		$this->search = '';
	}

	$this->depth++;

	foreach ($this->comments as $comment)
	{
		$this->view('comment')
		     ->set('option', $this->option)
		     ->set('comment', $comment)
		     ->set('post', $this->post)
		     ->set('unit', $this->unit)
		     ->set('lecture', $this->lecture)
		     ->set('config', $this->config)
		     ->set('depth', $this->depth)
		     ->set('cls', $cls)
		     ->set('base', $this->base)
		     ->set('attach', $this->attach)
		     ->set('search', $this->search)
		     ->set('course', $this->course)
		     ->display();
	}
}
?>
</ol>