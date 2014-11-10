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
switch ($this->filters['sortby'])
{
	case 'date_created': $show_date = 1; break;
	case 'date_modified': $show_date = 2; break;
	case 'date':
	default: $show_date = 3; break;
}

// Get version authors
$pa = new PublicationAuthor( $database );
?>

<ol class="results" id="publications">
<?php

foreach ($this->results as $line)
{

	$authors = $pa->getAuthors($line->version_id);

	// Get parameters
	$params = clone($this->config);
	$rparams = new JParameter( $line->params );
	$params->merge( $rparams );

	// Set the display date
	switch ($show_date)
	{
		case 0: $thedate = ''; break;
		case 1: $thedate = JHTML::_('date', $line->created, 'M d, Y');      break;
		case 2: $thedate = JHTML::_('date', $line->modified, 'M d, Y');     break;
		case 3: $thedate = JHTML::_('date', $line->published_up, 'M d, Y'); break;
	}

	// Display List of items
	$this->view('item')
	     ->set('option', 'com_publications')
	     ->set('filters', $this->filters)
	     ->set('config', $this->config)
	     ->set('helper', $this->helper)
	     ->set('authors', $authors)
	     ->set('line', $line)
	     ->set('thedate', $thedate)
	     ->set('params', $params)
	     ->display();
}
?>
</ol>