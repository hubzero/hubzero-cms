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

$database = JFactory::getDBO();

// Get version authors
$pa = new PublicationAuthor( $database );
?>

<ul class="mypubs">
<?php

foreach ($this->results as $row)
{
	// Get version authors
	$authors = $pa->getAuthors($row->version_id);

	$info = array();
	$info[] =  JHTML::_('date', $row->published_up, 'd M Y');
	$info[] = $row->cat_name;
	$info[] = JText::_('COM_PUBLICATIONS_CONTRIBUTORS') . ': '
		. $this->helper->showContributors( $authors, false, true );

	// Display List of items
	$this->view('_item')
	     ->set('option', 'com_publications')
	     ->set('row', $row)
	     ->set('info', $info)
	     ->display();
}
?>
</ul>