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

if ($this->comments && is_array($this->comments)) { ?>
	<ol class="comments">
<?php 
	$cls = 'odd';
	if (isset($this->cls))
	{
		$cls = ($this->cls == 'odd') ? 'even' : 'odd';
	}

	$this->depth++;

	foreach ($this->comments as $comment) 
	{
		//if ($comment->replies) 
		//{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => 'forum',
					'name'    => 'threads',
					'layout'  => 'comment'
				)
			);
			$view->option     = $this->option;
			$view->comment    = $comment;
			$view->post       = $this->post;
			$view->unit       = $this->unit;
			$view->lecture    = $this->lecture;
			$view->config     = $this->config;
			$view->depth      = $this->depth;
			$view->cls        = $cls;
			$view->base       = $this->base;
			$view->parser     = $this->parser;
			$view->wikiconfig = $this->wikiconfig;
			$view->attach     = $this->attach;
			$view->course     = $this->course;
			$view->display();
		//}
	}
?>
	</ol>
<?php } ?>