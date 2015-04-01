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

$author = $this->author;
$org = $author->organization ? $author->organization : $author->p_organization;
$name = $author->name ? $author->name : $author->p_name;
$name = trim($name) ? $name : $author->invited_name;
$name = trim($name) ? $name : $author->invited_email;
?>
	<span class="a-ordernum"></span>
	<?php if ($this->canedit) { ?>
	<span class="c-edit"><a href="<?php echo $this->url . '?vid=' . $this->vid .  '&amp;uid=' . $author->user_id . '&amp;move=' . $this->move . '&amp;action=editauthor&amp;owner=' . $this->owner; ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT'); ?></a></span>
	<?php } ?>
	<span class="a-wrap">
		<span class="a-authorname"><?php echo stripslashes($name); ?></span><span class="a-org"><?php echo $org ? ', '.stripslashes($org) : ''; ?></span>
		<span class="a-credit"><?php echo stripslashes($author->credit); ?></span>
	</span>

