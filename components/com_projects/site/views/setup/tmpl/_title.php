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

$title = $this->model->get('title') ? Lang::txt('COM_PROJECTS_NEW_PROJECT') . ': ' . $this->model->get('title') : $this->title;

?>
<header id="content-header">
	<h2><?php echo $title; ?> <?php if ($this->model->groupOwner() && $cn = $this->model->groupOwner('cn')) { ?> <?php echo Lang::txt('COM_PROJECTS_FOR').' '.ucfirst(Lang::txt('COM_PROJECTS_GROUP')); ?> <a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $cn); ?>"><?php echo \Hubzero\Utility\String::truncate($this->model->groupOwner('description'), 50); ?></a><?php } ?></h2>
</header><!-- / #content-header -->
