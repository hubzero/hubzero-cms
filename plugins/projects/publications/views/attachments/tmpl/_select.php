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

$prov 	= $this->pub->_project->provisioned == 1 ? 1 : 0;

switch ($this->type)
{
	case 'file':
	default:
		$active = 'files';
		break;
	case 'data':
		$active = 'databases';
		break;
	case 'link':
		$active = 'links';
		break;
}
$route = $prov
		? 'index.php?option=com_publications&task=submit&pid=' . $this->pub->id
		: 'index.php?option=com_projects&alias=' . $this->pub->_project->alias;
$selectUrl   = $prov
		? JRoute::_( $route) . '?active=' . $active . a . 'action=select' . a . 'p=' . $this->props
			. a . 'vid=' . $this->pub->version_id
		: JRoute::_( $route . '&active=' . $active . '&action=select') .'/?p=' . $this->props . '&pid='
			. $this->pub->id . '&vid=' . $this->pub->version_id;
?>
<div class="item-new">
	<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox nox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SELECT_' . strtoupper($this->type)); ?></a></span>
</div>