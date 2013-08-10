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

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}

$publishing = 
	is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
		.'com_publications' . DS . 'tables' . DS . 'publication.php')
	&& JPluginHelper::isEnabled('projects', 'publications')
	? 1 : 0;
	
if (!$publishing)
{
	return false;
}

$database 	=& JFactory::getDBO();

$filters = array();
$filters['sortby']   		= 'title';
$filters['sortdir']  		= 'ASC';
$filters['project']  		= $this->project->id;

// Get project publications
$objP  = new Publication($database);
$items = $objP->getRecords($filters);

// URL
$route 	= 'index.php?option=com_publications';

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'html.php');

if ($items) { 
?>
<div class="public-list-header">
	<h3><?php echo JText::_('COM_PROJECTS_PUBLICATIONS'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul>
		<?php foreach ($items as $item) { 
		?>
		<li class="publications"><a href="<?php echo JRoute::_($route . a . 'id=' . $item->id);  ?>"><?php echo $item->title; ?></a> <span class="public-list-info"> - <?php echo JText::_('COM_PROJECTS_PUBLISHED') . ' ' . JHTML::_('date', $item->published_up, $dateFormat, $tz) . ' ' . JText::_('COM_PROJECTS_IN') . ' <a href="'.JRoute::_('index.php?option=com_publications&category='.$item->cat_url).'">' . $item->cat_name . '</a>'; ?></span></li>	
		<?php
		} ?>
	</ul>
</div>
<?php } ?>
