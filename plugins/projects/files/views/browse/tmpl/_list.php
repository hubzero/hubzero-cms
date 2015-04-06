<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
defined('_JEXEC') or die( 'Restricted access' );

$c = $this->c;

foreach ($this->items as $item)
{
	$type = $item['type'];

	if ($type == 'folder')
	{
		$dir = $item['item'];

		// Folder view
		$this->view('folder', 'item')
		     ->set('subdir', $this->subdir)
		     ->set('item', $dir)
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->set('c', $c)
		     ->set('connect', $this->connect)
		     ->set('publishing', $this->publishing)
		     ->set('params', $this->fileparams)
		     ->set('case', $this->case)
		     ->set('url', $this->url)
		     ->display();
	}
	elseif ($type == 'document')
	{
		$file = $item['item'];

		// Hide gitignore file
		if ($file['name'] == '.gitignore')
		{
			if (count($this->items) == 1)
			{
				$empty = true;
			}
			continue;
		}

		// Document view
		$this->view('document', 'item')
		     ->set('subdir', $this->subdir)
		     ->set('item', $file)
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->set('c', $c)
		     ->set('connect', $this->connect)
		     ->set('publishing', $this->publishing)
		     ->set('params', $this->fileparams)
		     ->set('case', $this->case)
		     ->set('url', $this->url)
		     ->display();
	}
	elseif ($type == 'remote')
	{
		// Remote file
		$this->view($item['remote'], 'item')
		     ->set('subdir', $this->subdir)
		     ->set('item', $item['item'])
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->set('c', $c)
		     ->set('connect', $this->connect)
		     ->set('publishing', $this->publishing)
		     ->set('params', $this->fileparams)
		     ->set('case', $this->case)
		     ->set('url', $this->url)
		     ->display();
	}

	$c++;
}
?>
